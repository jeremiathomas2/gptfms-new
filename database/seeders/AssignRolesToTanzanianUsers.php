<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AssignRolesToTanzanianUsers extends Seeder
{
    public function run(): void
    {
        // Get all Tanzanian students
        $students = User::where('email', 'like', '%@student.ac.tz')->get();
        $studentRole = \Spatie\Permission\Models\Role::where('name', 'student')->first();
        
        $studentCount = 0;
        foreach ($students as $student) {
            try {
                if ($studentRole && !$student->hasRole('student')) {
                    $student->assignRole('student');
                    $studentCount++;
                }
            } catch (\Exception $e) {
                $this->command->error("Failed to assign student role to {$student->email}: " . $e->getMessage());
            }
        }

        // Get all Tanzanian supervisors
        $supervisors = User::where('email', 'like', '%@supervisor.ac.tz')->get();
        $supervisorRole = \Spatie\Permission\Models\Role::where('name', 'supervisor')->first();
        
        $supervisorCount = 0;
        foreach ($supervisors as $supervisor) {
            try {
                if ($supervisorRole && !$supervisor->hasRole('supervisor')) {
                    $supervisor->assignRole('supervisor');
                    $supervisorCount++;
                }
            } catch (\Exception $e) {
                $this->command->error("Failed to assign supervisor role to {$supervisor->email}: " . $e->getMessage());
            }
        }

        $this->command->info("Successfully assigned roles to {$studentCount} students and {$supervisorCount} supervisors!");
    }
}
