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
        Schema::create('group_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('participants_per_group')->default(4);
            $table->integer('countdown_minutes')->default(60);
            $table->timestamp('countdown_end_time')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('auto_create_groups')->default(true);
            $table->boolean('balance_by_gender')->default(true);
            $table->boolean('balance_by_skills')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_settings');
    }
};
