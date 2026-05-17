<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all users with student role
        $studentUsers = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', 'student')
            ->select('users.id')
            ->pluck('id');

        if ($studentUsers->isNotEmpty()) {
            // Update all student passwords to default 'password'
            DB::table('users')
                ->whereIn('id', $studentUsers)
                ->update([
                    'password' => Hash::make('password'),
                    'status' => 'active',
                    'updated_at' => now()
                ]);

            echo "Updated " . $studentUsers->count() . " student accounts to active status with default password.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be easily reversed as we don't know original passwords
        // In a production environment, you would want to backup passwords before changing them
        echo "Warning: This migration cannot be automatically reversed as original passwords are not stored.\n";
    }
};
