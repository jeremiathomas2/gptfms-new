<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\StudentSkillsSurvey;
use App\Models\SupervisorProfile;

class CreateTanzanianSampleData extends Seeder
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
            'Frances', 'Georgia', 'Heidi', 'Isabel', 'Jacqueline', 'Kimberly', 'Laura', 'Michelle', 'Nancy'
        ];

        $tanzanianLastNames = [
            'Mwanga', 'Kijazi', 'Moshi', 'Mcharo', 'Mkenda', 'Mlay', 'Mgeni', 'Mrema', 'Mteza', 'Mwaipaja',
            'Mwakasege', 'Mwalimu', 'Mwamba', 'Mwamakapa', 'Mwanga', 'Mwanyika', 'Mwakilima', 'Mwakalinga',
            'Mwakasanga', 'Mwakatumbula', 'Mwakikongwe', 'Mwakilima', 'Mwakulugulu', 'Mwakulugulu', 'Mwakulugulu',
            'Mwakulugulu', 'Mwakulugulu', 'Mwakulugulu', 'Mwakulugulu', 'Mwakulugulu', 'Mwakulugulu', 'Mwakulugulu',
            'Kimaro', 'Kinyashi', 'Kisanga', 'Kitula', 'Kiwanga', 'Komba', 'Komba', 'Kombo', 'Kondoro', 'Kongola',
            'Koto', 'Kweka', 'Kyara', 'Kyando', 'Kyando', 'Kyando', 'Kyando', 'Kyando', 'Kyando', 'Kyando'
        ];

        // Create 50 Tanzanian students
        $students = [];
        for ($i = 1; $i <= 50; $i++) {
            $firstName = $tanzanianFirstNames[array_rand($tanzanianFirstNames)];
            $lastName = $tanzanianLastNames[array_rand($tanzanianLastNames)];
            $fullName = $firstName . ' ' . $lastName;
            
            $students[] = [
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
            ];
        }

        // Insert students
        $studentUsers = [];
        foreach ($students as $studentData) {
            $student = User::create($studentData);
            $studentUsers[] = $student;
        }

        // Create 10 Tanzanian supervisors
        $supervisors = [];
        $supervisorTitles = ['Dr.', 'Prof.', 'Eng.', 'Mr.', 'Mrs.'];
        $departments = ['Computer Science', 'Information Technology', 'Software Engineering', 'Data Science', 'Cybersecurity', 'Artificial Intelligence'];
        
        for ($i = 1; $i <= 10; $i++) {
            $firstName = $tanzanianFirstNames[array_rand($tanzanianFirstNames)];
            $lastName = $tanzanianLastNames[array_rand($tanzanianLastNames)];
            $title = $supervisorTitles[array_rand($supervisorTitles)];
            $department = $departments[array_rand($departments)];
            $fullName = $title . ' ' . $firstName . ' ' . $lastName;
            
            $supervisors[] = [
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
            ];
        }

        // Insert supervisors
        $supervisorUsers = [];
        foreach ($supervisors as $supervisorData) {
            $supervisor = User::create($supervisorData);
            $supervisorUsers[] = $supervisor;
        }

        // Assign roles after all users are created
        foreach ($studentUsers as $student) {
            $student->assignRole('student');
            
            // Create skills survey data for some students
            if (rand(1, 3) > 1) { // 66% chance of having survey data
                StudentSkillsSurvey::create([
                    'user_id' => $student->id,
                    'skills' => json_encode([
                        'programming' => array_rand(['PHP', 'JavaScript', 'Python', 'Java', 'C++', 'Ruby', 'Go', 'Swift', 'Kotlin', 'TypeScript']),
                        'web' => array_rand(['HTML', 'CSS', 'React', 'Vue.js', 'Angular', 'Node.js', 'Laravel', 'Django', 'Flask', 'Spring Boot'])
                    ], JSON_UNESCAPED_UNICODE),
                    'experience_level' => array_rand(['beginner', 'intermediate', 'advanced']),
                    'interests' => json_encode(array_rand([
                        'web_development', 'mobile_development', 'data_science', 'machine_learning', 'artificial_intelligence',
                        'cybersecurity', 'database_management', 'software_engineering', 'game_development', 'ui_ux_design'
                    ]), JSON_UNESCAPED_UNICODE),
                    'project_type' => array_rand(['individual', 'team', 'both']),
                    'project_duration' => array_rand(['short', 'medium', 'long']),
                    'goals' => 'To become a skilled professional in my field and contribute to innovative projects that make a positive impact.',
                    'completed_at' => now()->subDays(rand(1, 60)),
                    'created_at' => now()->subDays(rand(1, 365)),
                    'updated_at' => now()->subDays(rand(0, 30)),
                ]);
            }
        }

        foreach ($supervisorUsers as $supervisor) {
            $supervisor->assignRole('supervisor');
            
            // Create supervisor profile
            SupervisorProfile::create([
                'user_id' => $supervisor->id,
                'department' => $departments[array_rand($departments)],
                'specialization' => array_rand([
                    'Software Architecture', 'Database Systems', 'Network Security', 'Machine Learning', 'Web Technologies',
                    'Mobile Development', 'Cloud Computing', 'DevOps', 'Quality Assurance', 'Project Management'
                ]),
                'experience_years' => rand(5, 25),
                'research_interests' => json_encode(array_rand([
                    'Artificial Intelligence', 'Machine Learning', 'Data Mining', 'Cloud Computing', 'IoT',
                    'Blockchain', 'Quantum Computing', 'Cybersecurity', 'Software Engineering', 'Human-Computer Interaction'
                ], rand(2, 4)), JSON_UNESCAPED_UNICODE),
                'publications' => rand(5, 50),
                'supervision_capacity' => rand(3, 10),
                'created_at' => now()->subDays(rand(365, 1825)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        $this->command->info('Created 50 Tanzanian students and 10 Tanzanian supervisors successfully!');
    }
}
