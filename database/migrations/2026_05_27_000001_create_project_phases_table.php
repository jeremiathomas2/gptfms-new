<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('phase_number');
            $table->string('phase_title');
            $table->longText('submission')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->enum('status', ['not_started', 'submitted', 'approved', 'changes_requested'])->default('not_started');
            $table->longText('supervisor_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'phase_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_phases');
    }
};

