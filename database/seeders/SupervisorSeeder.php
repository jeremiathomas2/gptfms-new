<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class SupervisorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $supervisors = [
            [
                'name' => 'John Anderson',
                'first_name' => 'John',
                'last_name' => 'Anderson',
                'email' => 'john.anderson.supervisor1@university.edu',
                'password' => Hash::make('password123'),
                'phone' => '+1-555-0101',
                'gender' => 'male',
                'registration_number' => 'SUP001',
                'status' => 'active',
            ],
            [
                'name' => 'Sarah Mitchell',
                'first_name' => 'Sarah',
                'last_name' => 'Mitchell',
                'email' => 'sarah.mitchell.supervisor2@university.edu',
                'password' => Hash::make('password123'),
                'phone' => '+1-555-0102',
                'gender' => 'female',
                'registration_number' => 'SUP002',
                'status' => 'active',
            ],
            [
                'name' => 'Michael Chen',
                'first_name' => 'Michael',
                'last_name' => 'Chen',
                'email' => 'michael.chen.supervisor3@university.edu',
                'password' => Hash::make('password123'),
                'phone' => '+1-555-0103',
                'gender' => 'male',
                'registration_number' => 'SUP003',
                'status' => 'active',
            ]
        ];

        foreach ($supervisors as $supervisorData) {
            $user = User::create($supervisorData);
            
            // Assign supervisor role
            $user->assignRole('supervisor');
            
            // Create supervisor profile
            $user->supervisorProfile()->create([
                'department' => 'Computer Science',
                'position' => 'Senior Supervisor',
                'specializations' => json_encode(['Software Engineering', 'Web Development', 'Database Management']),
                'years_of_experience' => 5,
                'highest_education' => 'Master of Science',
                'certifications' => json_encode(['PMP', 'AWS Solutions Architect', 'Scrum Master']),
                'preferences' => json_encode(['evening_shifts', 'remote_work']),
                'max_students' => 15,
                'is_available' => true,
            ]);
        }

        $this->command->info('Sample supervisors created successfully!');
    }
}
