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
        Schema::table('answers', function (Blueprint $table) {
            $table->index(['room_id', 'user_id', 'question_id']);
        });

        Schema::table('scores', function (Blueprint $table) {
            $table->index(['room_id', 'total_score']);
        });

        Schema::table('room_participants', function (Blueprint $table) {
            $table->index(['room_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->dropIndex(['room_id', 'user_id', 'question_id']);
        });

        Schema::table('scores', function (Blueprint $table) {
            $table->dropIndex(['room_id', 'total_score']);
        });

        Schema::table('room_participants', function (Blueprint $table) {
            $table->dropIndex(['room_id', 'user_id']);
        });
    }
};
