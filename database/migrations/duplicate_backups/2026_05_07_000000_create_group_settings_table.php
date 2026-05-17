<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('group_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('participants_per_group')->default(4);
            $table->integer('countdown_minutes')->default(60);
            $table->boolean('balance_by_gender')->default(true);
            $table->boolean('balance_by_skills')->default(true);
            $table->boolean('auto_create_groups')->default(true);
            $table->boolean('is_active')->default(false);
            $table->timestamp('countdown_start_time')->nullable();
            $table->timestamp('countdown_end_time')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_settings');
    }
};
