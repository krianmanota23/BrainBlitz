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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('room_code')->unique();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->enum('topic_mode', ['single', 'multiple', 'random']);
            $table->integer('time_per_question')->default(30);
            $table->integer('max_participants')->default(30);
            $table->enum('status', ['draft', 'waiting', 'ongoing', 'finished'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
