<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $request->validate([
            'file' => 'required|mimes:csv,txt',
            'type' => 'required|in:student,supervisor'
        ]);

        $file = $request->file('file');
        $type = $request->input('type');
        $handle = fopen($file->getRealPath(), "r");
        $header = fgetcsv($handle, 1000, ",");
        
        // Normalize headers to lowercase to avoid issues
        $header = array_map('strtolower', $header);
        
        $count = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($header) !== count($data)) {
                    $errors[] = "Skipping row: column count mismatch.";
                    continue;
                }
                
                $row = array_combine($header, $data);
                
                $email = $row['email'] ?? null;
                if (!$email || User::where('email', $email)->exists()) {
                    $errors[] = "Skipping " . ($email ?? "unknown") . ": already exists or email missing.";
                    continue;
                }

                $user = User::create([
                    'first_name' => $row['firstname'] ?? '',
                    'middle_name' => $row['middlename'] ?? null,
                    'last_name' => $row['lastname'] ?? '',
                    'name' => trim(($row['firstname'] ?? '') . ' ' . ($row['middlename'] ?? '') . ' ' . ($row['lastname'] ?? '')),
                    'email' => $email,
                    'phone' => $row['phone_number'] ?? null,
                    'gender' => strtolower($row['gender'] ?? 'other'),
                    'registration_number' => $row['registration_number'] ?? ($type === 'student' ? ('REG-' . strtoupper(Str::random(8))) : null),
                    'password' => Hash::make('password'),
                    'status' => 'active',
                ]);

                $user->assignRole($type);
                $count++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Import failed: ' . $e->getMessage()], 500);
        }

        fclose($handle);

        return response()->json([
            'success' => true, 
            'message' => "Successfully imported $count users.",
            'errors' => $errors
        ]);
    }
}
