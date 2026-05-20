<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use App\Notifications\UserCreatedNotification;

class UserImportController extends Controller
{
    public function downloadTemplate($type)
    {
        $headers = [];
        $filename = "";

        if ($type === 'supervisor') {
            $headers = ['firstname', 'Middlename', 'lastname', 'gender', 'phone_number', 'email'];
            $filename = "supervisor_template.csv";
        } else {
            $headers = ['firstname', 'Middlename', 'lastname', 'gender', 'phone_number', 'registration_number', 'email'];
            $filename = "student_template.csv";
        }

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }

    public function import(Request $request)
    {
        set_time_limit(300); // Increase time limit to 5 minutes

        $request->validate([
            'file' => 'required|mimes:csv,txt',
            'type' => 'required|in:student,supervisor'
        ]);

        $file = $request->file('file');
        $type = $request->input('type');
        $handle = fopen($file->getRealPath(), "r");
        
        // Handle BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle, 1000, ",");
        if (!$header) {
            fclose($handle);
            return response()->json(['success' => false, 'message' => 'The CSV file is empty.'], 422);
        }

        // Normalize headers: trim, lowercase, remove spaces/underscores for better matching
        $header = array_map(function($h) {
            return strtolower(str_replace([' ', '_'], '', trim($h)));
        }, $header);
        
        $count = 0;
        $errors = [];

        try {
            DB::beginTransaction();
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Skip empty lines
                if (empty($data) || (count($data) === 1 && empty($data[0]))) {
                    continue;
                }

                if (count($header) !== count($data)) {
                    $errors[] = "Skipping row: column count mismatch (Expected " . count($header) . ", got " . count($data) . ").";
                    continue;
                }
                
                $row = array_combine($header, $data);
                
                // Map common header variations
                $email = $row['email'] ?? null;
                $firstName = $row['firstname'] ?? $row['firstname'] ?? '';
                $middleName = $row['middlename'] ?? $row['middlename'] ?? null;
                $lastName = $row['lastname'] ?? $row['lastname'] ?? '';
                $phone = $row['phonenumber'] ?? $row['phone'] ?? $row['phone_number'] ?? null;
                $gender = $row['gender'] ?? 'other';
                $regNumber = $row['registrationnumber'] ?? $row['regnumber'] ?? $row['registration_number'] ?? null;

                if (!$email) {
                    $errors[] = "Skipping row: Email address is missing.";
                    continue;
                }

                if (User::where('email', $email)->exists()) {
                    $errors[] = "Skipping {$email}: User with this email already exists.";
                    continue;
                }

                $password = 'password';
                $user = User::create([
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'name' => trim($firstName . ' ' . ($middleName ? $middleName . ' ' : '') . $lastName),
                    'email' => $email,
                    'phone' => $phone,
                    'gender' => strtolower(trim($gender)),
                    'registration_number' => $regNumber ?? ($type === 'student' ? ('REG-' . strtoupper(Str::random(8))) : null),
                    'password' => Hash::make($password),
                    'status' => 'active',
                    'email_verified_at' => now(), // Auto-verify to allow immediate login
                ]);

                $user->assignRole($type);
                
                // Send notifications
                try {
                    $notification = new UserCreatedNotification($password);
                    $user->notify($notification);
                    $notification->sendSms($user);
                } catch (\Exception $e) {
                    Log::error("Notification failed for {$email}: " . $e->getMessage());
                    // We don't skip the user if notification fails
                }

                $count++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Import Exception: " . $e->getMessage());
            if (isset($handle)) fclose($handle);
            return response()->json(['success' => false, 'message' => 'Import failed: ' . $e->getMessage()], 500);
        }

        if (isset($handle)) fclose($handle);

        return response()->json([
            'success' => true, 
            'message' => "Successfully imported $count users.",
            'errors' => $errors
        ]);
    }
}
