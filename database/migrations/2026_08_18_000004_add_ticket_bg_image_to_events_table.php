<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'ticket_bg_image')) {
                $table->string('ticket_bg_image')->nullable()->after('pdf_terms_text');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'ticket_bg_image')) {
                $table->dropColumn('ticket_bg_image');
            }
        });
    }
};
