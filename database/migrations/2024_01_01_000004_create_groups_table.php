<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['forming', 'active', 'completed', 'archived'])->default('forming');
            $table->integer('max_members')->default(4);
            $table->foreignId('project_id')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->json('formation_criteria')->nullable(); // How the group was formed
            $table->decimal('formation_score', 5, 2)->nullable(); // AI scoring for group quality
            $table->timestamp('formed_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
