<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('meeting_number');
            $table->date('meeting_date');
            $table->string('location')->nullable();
            $table->string('title');
            $table->text('agenda')->nullable();
            $table->json('attendee_ids')->nullable();
            $table->unsignedInteger('attendee_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['group_id', 'meeting_number']);
            $table->index(['group_id', 'meeting_date']);
            $table->index(['supervisor_id', 'meeting_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_attendances');
    }
};
