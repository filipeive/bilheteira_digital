<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ticket_code', 12)->unique();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('buyer_name');
            $table->string('buyer_phone', 20);
            $table->string('buyer_email')->nullable();
            $table->enum('ticket_type', ['promotional', 'second_lot', 'gate', 'vip', 'free']);
            $table->integer('price')->default(0);
            $table->string('payment_ref')->nullable();
            $table->enum('payment_method', ['mpesa', 'emola', 'cash', 'free'])->default('mpesa');
            $table->enum('status', ['pending', 'confirmed', 'used', 'cancelled'])->default('pending');
            $table->text('qr_payload')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->foreignId('scanned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'status']);
            $table->index('buyer_phone');
            $table->index('ticket_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
