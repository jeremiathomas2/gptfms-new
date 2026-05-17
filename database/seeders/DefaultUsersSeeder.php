<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DefaultUsersSeeder extends Seeder
{
    public function run()
    {
        // Clear existing users and roles
        DB::table('users')->delete();
        DB::table('model_has_roles')->delete();
        
        // Create Admin user
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@gptfms.com',
            'password' => Hash::make('password'),
            'registration_number' => 'ADMIN_001',
            'phone' => '+1234567890',
            'bio' => 'System Administrator',
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $admin->assignRole('admin');
        
        // Create Supervisor user
        $supervisor = User::create([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john.smith@university.edu',
            'password' => Hash::make('password'),
            'registration_number' => 'SUP_002',
            'phone' => '+1234567891',
            'bio' => 'Experienced supervisor with expertise in software development and project management.',
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $supervisor->assignRole('supervisor');
        
        // Create Student user
        $student = User::create([
            'first_name' => 'Alice',
            'last_name' => 'Wilson',
            'email' => 'alice.wilson@student.edu',
            'password' => Hash::make('password'),
            'registration_number' => 'STU_003',
            'phone' => '+1234567892',
            'bio' => 'Computer Science student interested in web development and machine learning.',
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $student->assignRole('student');
        
        $this->command->info('Default users created successfully:');
        $this->command->info('Admin: admin@gptfms.com (password)');
        $this->command->info('Supervisor: john.smith@university.edu (password)');
        $this->command->info('Student: alice.wilson@student.edu (password)');
    }
}
