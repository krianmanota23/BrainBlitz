<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            if (!Schema::hasColumn('scores', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            if (Schema::hasColumn('scores', 'created_at')) {
                $table->dropColumn('created_at');
            }
        });
    }
};
