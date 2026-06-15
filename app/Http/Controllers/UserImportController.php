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
    private function normalizeTzPhone(?string $value): ?string
    {
        $raw = trim((string) ($value ?? ''));
        if ($raw === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '255') && strlen($digits) === 12) {
            return $digits;
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            return '255' . substr($digits, 1);
        }

        if (strlen($digits) === 9) {
            return '255' . $digits;
        }

        return $digits;
    }

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
            if (is_resource($handle)) fclose($handle);
            return response()->json(['success' => false, 'message' => 'The CSV file is empty.'], 422);
        }

        $header = array_map(function($h) {
            return strtolower(str_replace([' ', '_'], '', trim($h)));
        }, $header);
        
        $count = 0;
        $errors = [];
        $duplicates = [];
        
        // --- OPTIMIZATION: Pre-fetch all data for duplicate check ---
        $rows = [];
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (empty($data) || (count($data) === 1 && empty($data[0]))) continue;
            if (count($header) !== count($data)) {
                $errors[] = "Skipping row: column count mismatch.";
                continue;
            }
            $rows[] = array_combine($header, $data);
        }
        fclose($handle);

        $emails = collect($rows)->pluck('email')->filter()->toArray();
        $phones = collect($rows)
            ->map(fn ($r) => $this->normalizeTzPhone($r['phonenumber'] ?? ($r['phone'] ?? null)))
            ->filter()
            ->values()
            ->toArray();
        $regNumbers = collect($rows)->pluck('registrationnumber')->merge(collect($rows)->pluck('regnumber'))->filter()->toArray();

        $existingUsers = User::whereIn('email', $emails)
            ->orWhereIn('phone', $phones)
            ->orWhereIn('registration_number', $regNumbers)
            ->get();

        $existingEmails = $existingUsers->pluck('email')->toArray();
        $existingPhones = $existingUsers->pluck('phone')->toArray();
        $existingRegs = $existingUsers->pluck('registration_number')->toArray();

        foreach ($rows as $row) {
            $email = $row['email'] ?? null;
            $firstName = $row['firstname'] ?? '';
            $lastName = $row['lastname'] ?? '';
            $phone = $this->normalizeTzPhone($row['phonenumber'] ?? ($row['phone'] ?? null));
            $regNumber = $row['registrationnumber'] ?? $row['regnumber'] ?? null;

            if (!$email) {
                $errors[] = "Skipping row: Email address is missing.";
                continue;
            }

            if (in_array($email, $existingEmails) || ($phone && in_array($phone, $existingPhones)) || ($regNumber && in_array($regNumber, $existingRegs))) {
                $reason = in_array($email, $existingEmails) ? 'Email' : (in_array($phone, $existingPhones) ? 'Phone' : 'Reg Number');
                $duplicates[] = "{$firstName} {$lastName} ({$email}) - Duplicate found by {$reason}";
                continue;
            }
        }

        if (!empty($duplicates)) {
            return response()->json([
                'success' => false,
                'message' => 'Duplicates detected. Please crosscheck the CSV file.',
                'duplicates' => $duplicates
            ], 422);
        }

        try {
            $createdUsers = [];
            DB::beginTransaction();
            foreach ($rows as $row) {
                $email = $row['email'];
                $firstName = $row['firstname'] ?? '';
                $middleName = $row['middlename'] ?? null;
                $lastName = $row['lastname'] ?? '';
                $phone = $this->normalizeTzPhone($row['phonenumber'] ?? ($row['phone'] ?? null));
                $regNumber = $row['registrationnumber'] ?? $row['regnumber'] ?? null;
                $gender = $row['gender'] ?? 'other';

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
                if ($type === 'supervisor') {
                    $user->supervisorProfile()->firstOrCreate(
                        ['user_id' => $user->id],
                        ['is_available' => true]
                    );
                }
                $createdUsers[] = [$user, $password, $type];

                $count++;
            }
            DB::commit();

            $queued = 0;
            foreach ($createdUsers as [$user, $password, $type]) {
                try {
                    $user->notify(new WelcomeNotification($password, $type));
                    $queued++;
                } catch (\Throwable $e) {
                    Log::error("Welcome notification failed for {$user->email}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Import Exception: " . $e->getMessage());
            if (isset($handle) && is_resource($handle)) fclose($handle);
            return response()->json(['success' => false, 'message' => 'Import failed: ' . $e->getMessage()], 500);
        }

        if (isset($handle) && is_resource($handle)) fclose($handle);

        return response()->json([
            'success' => true, 
            'message' => "Successfully imported $count users.",
            'queued' => $queued ?? 0,
            'errors' => $errors
        ]);
    }
}
