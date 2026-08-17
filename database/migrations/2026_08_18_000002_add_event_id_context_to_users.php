<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'default_event_id')) {
                $table->foreignId('default_event_id')
                      ->nullable()
                      ->constrained('events')
                      ->nullOnDelete()
                      ->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'default_event_id')) {
                $table->dropForeign(['default_event_id']);
                $table->dropColumn('default_event_id');
            }
        });
    }
};
