<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('student_id')->unique();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->string('major')->nullable();
            $table->integer('semester')->nullable();
            $table->json('skills')->nullable(); // Array of skills with proficiency levels
            $table->json('availability')->nullable(); // Weekly availability schedule
            $table->json('personality_traits')->nullable(); // Personality/working style questionnaire results
            $table->text('bio')->nullable();
            $table->enum('preferred_group_size', [2, 3, 4, 5, 6])->default(4);
            $table->json('preferred_roles')->nullable(); // Leader, Researcher, Developer, etc.
            $table->integer('total_projects')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
