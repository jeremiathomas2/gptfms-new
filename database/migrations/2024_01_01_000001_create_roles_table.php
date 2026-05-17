<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->string('guard_name')->default('web');
            $table->timestamps();
        });

        // Insert default roles
        DB::table('roles')->insert([
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'System administrator', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'supervisor', 'display_name' => 'Supervisor', 'description' => 'Lecturer/Supervisor', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'student', 'display_name' => 'Student', 'description' => 'Student user', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
