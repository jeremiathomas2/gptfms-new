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
        Schema::table('users', function (Blueprint $table) {
            $table->string('registration_number', 50)->nullable()->after('phone');
        });
        
        // Update existing users to have unique registration numbers
        \DB::table('users')->whereNull('registration_number')->update([
            'registration_number' => \DB::raw('CONCAT("REG_", id, "_", DATE_FORMAT(created_at, "%Y%m%d"))')
        ]);
        
        // Now make the column unique and not nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('registration_number', 50)->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('registration_number');
        });
    }
};
