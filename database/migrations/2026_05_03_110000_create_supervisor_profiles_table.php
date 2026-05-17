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
        Schema::create('supervisor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->text('bio')->nullable();
            $table->string('specialization')->nullable(); // Add this
            $table->json('specializations')->nullable(); // Areas of expertise
            $table->integer('experience_years')->nullable(); // Add this
            $table->integer('years_of_experience')->nullable();
            $table->json('research_interests')->nullable(); // Add this
            $table->integer('publications')->nullable(); // Add this
            $table->integer('supervision_capacity')->nullable(); // Add this
            $table->string('highest_education')->nullable();
            $table->string('certifications')->nullable();
            $table->json('preferences')->nullable(); // Student preferences, project preferences
            $table->integer('max_students')->default(10); // Maximum students to supervise
            $table->boolean('is_available')->default(true);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            
            // Add indexes
            $table->index('user_id');
            $table->index('department');
            $table->index('is_available');
            $table->index('last_activity_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisor_profiles');
    }
};
