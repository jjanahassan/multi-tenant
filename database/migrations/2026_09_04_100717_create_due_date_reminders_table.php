<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('due_date_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignee_id')->constrained('users')->cascadeOnDelete();
            $table->date('due_date');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique([
                'task_id',
                'assignee_id',
                'due_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('due_date_reminders');
    }
};