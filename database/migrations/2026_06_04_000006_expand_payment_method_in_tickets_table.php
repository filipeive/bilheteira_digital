<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Convert payment_method from enum to varchar to support new methods
        // Preserves all existing values (mpesa, emola, cash, free)
        DB::statement("ALTER TABLE tickets MODIFY COLUMN payment_method VARCHAR(255) NOT NULL DEFAULT 'cash'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tickets MODIFY COLUMN payment_method ENUM('mpesa','emola','cash','free') NOT NULL DEFAULT 'cash'");
    }
};
