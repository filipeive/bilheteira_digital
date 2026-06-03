<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->timestamp('email_sent_at')->nullable()->after('status');
            $table->timestamp('whatsapp_sent_at')->nullable()->after('email_sent_at');
            $table->timestamp('reminder_sent_at')->nullable()->after('whatsapp_sent_at');
            $table->string('ticket_mode')->default('personalized')->after('reminder_sent_at');
            $table->unsignedBigInteger('batch_id')->nullable()->after('ticket_mode');
            $table->string('scanned_device')->nullable()->after('scanned_by');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'email_sent_at', 'whatsapp_sent_at', 'reminder_sent_at',
                'ticket_mode', 'batch_id', 'scanned_device',
            ]);
        });
    }
};
