<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'artists')) {
                $table->string('artists')->nullable()->after('organizer_name');
            }
            if (!Schema::hasColumn('events', 'artist_photo_1')) {
                $table->string('artist_photo_1')->nullable()->after('artists');
            }
            if (!Schema::hasColumn('events', 'artist_photo_2')) {
                $table->string('artist_photo_2')->nullable()->after('artist_photo_1');
            }
            if (!Schema::hasColumn('events', 'pdf_brand_text')) {
                $table->string('pdf_brand_text')->nullable()->after('artist_photo_2');
            }
            if (!Schema::hasColumn('events', 'pdf_terms_text')) {
                $table->text('pdf_terms_text')->nullable()->after('pdf_brand_text');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'artists',
                'artist_photo_1',
                'artist_photo_2',
                'pdf_brand_text',
                'pdf_terms_text',
            ]);
        });
    }
};
