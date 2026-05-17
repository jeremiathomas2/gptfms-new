<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peer_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('evaluated_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->integer('contribution_score'); // 1-5 scale
            $table->integer('teamwork_score'); // 1-5 scale
            $table->integer('communication_score'); // 1-5 scale
            $table->integer('quality_score'); // 1-5 scale
            $table->integer('timeliness_score'); // 1-5 scale
            $table->decimal('overall_score', 5, 2)->storedAs('(contribution_score + teamwork_score + communication_score + quality_score + timeliness_score) / 5.0');
            $table->text('comments')->nullable();
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['evaluator_id', 'evaluated_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peer_evaluations');
    }
};
