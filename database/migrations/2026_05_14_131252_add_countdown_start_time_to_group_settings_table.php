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
        Schema::table('group_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('group_settings', 'countdown_start_time')) {
                $table->timestamp('countdown_start_time')->nullable()->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_settings', function (Blueprint $table) {
            if (Schema::hasColumn('group_settings', 'countdown_start_time')) {
                $table->dropColumn('countdown_start_time');
            }
        });
    }
};
