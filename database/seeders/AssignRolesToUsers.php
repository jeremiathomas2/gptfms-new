<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AssignRolesToUsers extends Seeder
{
    public function run(): void
    {
        // Create roles if they don't exist
        $roles = ['admin', 'supervisor', 'student'];
        
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
        
        $this->command->info('Roles created successfully');
        
        // Assign roles to users
        $admin = User::where('email', 'admin@gptfms.com')->first();
        if ($admin) {
            $admin->assignRole('admin');
            $this->command->info('Admin role assigned to admin@gptfms.com');
        }
        
        $supervisor = User::where('email', 'john.smith@university.edu')->first();
        if ($supervisor) {
            $supervisor->assignRole('supervisor');
            $this->command->info('Supervisor role assigned to john.smith@university.edu');
        }
        
        $student = User::where('email', 'alice.wilson@student.edu')->first();
        if ($student) {
            $student->assignRole('student');
            $this->command->info('Student role assigned to alice.wilson@student.edu');
        }
        
        $this->command->info('All roles assigned successfully');
    }
}
