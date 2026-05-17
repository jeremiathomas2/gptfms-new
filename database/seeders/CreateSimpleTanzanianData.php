<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CreateSimpleTanzanianData extends Seeder
{
    public function run(): void
    {
        // Tanzanian first names and last names
        $tanzanianFirstNames = [
            'Aisha', 'Fatuma', 'Mariam', 'Zawadi', 'Grace', 'Hawa', 'Khadija', 'Rehema', 'Neema', 'Joyce',
            'Anna', 'Elizabeth', 'Miriam', 'Sarah', 'Rachel', 'Esther', 'Ruth', 'Naomi', 'Deborah', 'Judith',
            'Magreth', 'Paula', 'Christina', 'Beatrice', 'Dorothy', 'Flora', 'Gloria', 'Helen', 'Irene',
            'Janet', 'Katherine', 'Lucy', 'Martha', 'Nancy', 'Olivia', 'Patricia', 'Rosemary', 'Susan', 'Teresa',
            'Victoria', 'Winnie', 'Yasmin', 'Zara', 'Abigail', 'Brenda', 'Caroline', 'Diana', 'Evelyn',
            'Frances', 'Georgia', 'Heidi', 'Isabel', 'Jacqueline', 'Kimberly', 'Laura', 'Michelle'
        ];

        $tanzanianLastNames = [
            'Mwanga', 'Kijazi', 'Moshi', 'Mcharo', 'Mkenda', 'Mlay', 'Mgeni', 'Mrema', 'Mteza', 'Mwaipaja',
            'Mwakasege', 'Mwalimu', 'Mwamba', 'Mwamakapa', 'Mwanga', 'Mwanyika', 'Mwakilima', 'Mwakalinga',
            'Mwakasanga', 'Mwakatumbula', 'Mwakikongwe', 'Mwakilima', 'Mwakulugulu', 'Mkongwa', 'Mkumbwa',
            'Kimaro', 'Kinyashi', 'Kisanga', 'Kitula', 'Kiwanga', 'Komba', 'Kombo', 'Kondoro', 'Kongola',
            'Koto', 'Kweka', 'Kyara', 'Kyando', 'Lukindo', 'Magesa', 'Mahay', 'Makame', 'Malima', 'Massawe'
        ];

        // Create 50 Tanzanian students
        $studentCount = 0;
        for ($i = 1; $i <= 50; $i++) {
            $firstName = $tanzanianFirstNames[array_rand($tanzanianFirstNames)];
            $lastName = $tanzanianLastNames[array_rand($tanzanianLastNames)];
            $fullName = $firstName . ' ' . $lastName;
            
            try {
                $student = User::create([
                    'name' => $fullName,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => strtolower(str_replace(' ', '', $firstName)) . '.' . strtolower(str_replace(' ', '', $lastName)) . $i . '@student.ac.tz',
                    'password' => Hash::make('password123'),
                    'status' => 'active',
                    'registration_number' => 'TU' . str_pad($i + 100, 4, '0', STR_PAD_LEFT) . '/2024',
                    'phone' => '+255' . rand(700000000, 799999999),
                    'created_at' => now()->subDays(rand(1, 365)),
                    'updated_at' => now()->subDays(rand(0, 30)),
                ]);
                
                // Assign student role if possible
                try {
                    $student->assignRole('student');
                    $studentCount++;
                } catch (\Exception $e) {
                    // Skip role assignment if tables don't exist
                    $this->command->info("Created student {$student->name} (role assignment skipped)");
                }
                
            } catch (\Exception $e) {
                $this->command->error("Failed to create student {$fullName}: " . $e->getMessage());
            }
        }

        // Create 10 Tanzanian supervisors
        $supervisorTitles = ['Dr.', 'Prof.', 'Eng.', 'Mr.', 'Mrs.'];
        $departments = ['Computer Science', 'Information Technology', 'Software Engineering', 'Data Science', 'Cybersecurity', 'Artificial Intelligence'];
        $supervisorCount = 0;
        
        for ($i = 1; $i <= 10; $i++) {
            $firstName = $tanzanianFirstNames[array_rand($tanzanianFirstNames)];
            $lastName = $tanzanianLastNames[array_rand($tanzanianLastNames)];
            $title = $supervisorTitles[array_rand($supervisorTitles)];
            $department = $departments[array_rand($departments)];
            $fullName = $title . ' ' . $firstName . ' ' . $lastName;
            
            try {
                $supervisor = User::create([
                    'name' => $fullName,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => strtolower(str_replace(' ', '', $firstName)) . '.' . strtolower(str_replace(' ', '', $lastName)) . $i . '@supervisor.ac.tz',
                    'password' => Hash::make('password123'),
                    'status' => 'active',
                    'registration_number' => 'SU' . str_pad($i + 200, 4, '0', STR_PAD_LEFT) . '/2024',
                    'phone' => '+255' . rand(600000000, 699999999),
                    'created_at' => now()->subDays(rand(365, 1825)), // 1-5 years ago
                    'updated_at' => now()->subDays(rand(0, 30)),
                ]);
                
                // Assign supervisor role if possible
                try {
                    $supervisor->assignRole('supervisor');
                    $supervisorCount++;
                } catch (\Exception $e) {
                    // Skip role assignment if tables don't exist
                    $this->command->info("Created supervisor {$supervisor->name} (role assignment skipped)");
                }
                
            } catch (\Exception $e) {
                $this->command->error("Failed to create supervisor {$fullName}: " . $e->getMessage());
            }
        }

        $this->command->info("Successfully created {$studentCount} Tanzanian students and {$supervisorCount} Tanzanian supervisors!");
        $this->command->info("Default password for all accounts: password123");
    }
}
