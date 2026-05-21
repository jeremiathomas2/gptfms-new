<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use App\Notifications\WelcomeNotification;

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
        set_time_limit(300);

        $request->validate([
            'file' => 'required|mimes:csv,txt',
            'type' => 'required|in:student,supervisor'
        ]);

        $file = $request->file('file');
        $type = $request->input('type');
        $handle = fopen($file->getRealPath(), "r");
        
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle, 1000, ",");
        if (!$header) {
            fclose($handle);
            return response()->json(['success' => false, 'message' => 'The CSV file is empty.'], 422);
        }

        $header = array_map(function($h) {
            return strtolower(str_replace([' ', '_'], '', trim($h)));
        }, $header);
        
        $count = 0;
        $errors = [];
        $duplicates = [];

        try {
            DB::beginTransaction();
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (empty($data) || (count($data) === 1 && empty($data[0]))) {
                    continue;
                }

                if (count($header) !== count($data)) {
                    $errors[] = "Skipping row: column count mismatch.";
                    continue;
                }
                
                $row = array_combine($header, $data);
                
                $email = $row['email'] ?? null;
                $firstName = $row['firstname'] ?? '';
                $middleName = $row['middlename'] ?? null;
                $lastName = $row['lastname'] ?? '';
                $phone = $row['phonenumber'] ?? $row['phone'] ?? null;
                $regNumber = $row['registrationnumber'] ?? $row['regnumber'] ?? null;
                $gender = $row['gender'] ?? 'other';

                if (!$email) {
                    $errors[] = "Skipping row: Email address is missing.";
                    continue;
                }

                // Check for duplicates
                $existingUser = User::where('email', $email)
                    ->orWhere(function($q) use ($phone) {
                        if ($phone) $q->where('phone', $phone);
                    })
                    ->orWhere(function($q) use ($regNumber) {
                        if ($regNumber) $q->where('registration_number', $regNumber);
                    })
                    ->first();

                if ($existingUser) {
                    $duplicates[] = "{$firstName} {$lastName} ({$email}) - Duplicate found by " . 
                        ($existingUser->email === $email ? 'Email' : 
                        ($existingUser->phone === $phone ? 'Phone' : 'Reg Number'));
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
                    'email_verified_at' => now(),
                ]);

                $user->assignRole($type);
                
                try {
                    $notification = new WelcomeNotification($password, $type);
                    $user->notify($notification);
                    $notification->sendSms($user);
                } catch (\Exception $e) {
                    Log::error("Notification failed for {$email}: " . $e->getMessage());
                }

                $count++;
            }

            if (!empty($duplicates)) {
                DB::rollBack();
                fclose($handle);
                return response()->json([
                    'success' => false,
                    'message' => 'Duplicates detected. Please crosscheck the CSV file.',
                    'duplicates' => $duplicates
                ], 422);
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
