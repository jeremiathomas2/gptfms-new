<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['leader', 'member', 'researcher', 'developer', 'designer'])->default('member');
            $table->enum('status', ['invited', 'joined', 'left'])->default('joined');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->decimal('contribution_score', 5, 2)->default(0.00);
            $table->integer('tasks_completed')->default(0);
            $table->timestamps();

            $table->unique(['group_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};
