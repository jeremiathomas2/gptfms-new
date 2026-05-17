<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_skills_survey', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('skills')->nullable(); // Programming languages, web technologies, etc.
            $table->string('experience_level')->nullable();
            $table->json('interests')->nullable(); // Areas of interest
            $table->string('project_type')->nullable();
            $table->string('project_duration')->nullable();
            $table->text('goals')->nullable(); // User's goals and aspirations
            $table->timestamp('completed_at');
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('user_id');
            $table->index('experience_level');
            $table->index('project_type');
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_skills_survey');
    }
};
