<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('supervisor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('status', ['draft', 'active', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0.00);
            $table->json('requirements')->nullable(); // Project requirements and specifications
            $table->json('deliverables')->nullable(); // Expected deliverables
            $table->string('course_code')->nullable();
            $table->decimal('max_grade', 5, 2)->default(100.00);
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
