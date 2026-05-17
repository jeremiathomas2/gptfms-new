<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CreateTestUsers extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'System Administrator',
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'email' => 'admin@gptfms.com',
            'password' => Hash::make('password'),
            'status' => 'active',
            'registration_number' => 'ADMIN001',
        ]);

        // Create supervisor user
        $supervisor = User::create([
            'name' => 'John Smith',
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john.smith@university.edu',
            'password' => Hash::make('password'),
            'status' => 'active',
            'registration_number' => 'SUP001',
        ]);

        // Create student user
        $student = User::create([
            'name' => 'Alice Wilson',
            'first_name' => 'Alice',
            'last_name' => 'Wilson',
            'email' => 'alice.wilson@student.edu',
            'password' => Hash::make('password'),
            'status' => 'active',
            'registration_number' => 'STU001',
        ]);

        $this->command->info('Test users created successfully:');
        $this->command->info('Admin: admin@gptfms.com / password');
        $this->command->info('Supervisor: john.smith@university.edu / password');
        $this->command->info('Student: alice.wilson@student.edu / password');
    }
}
