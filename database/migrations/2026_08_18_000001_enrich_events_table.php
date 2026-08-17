<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'slug')) {
                $table->string('slug')->unique()->nullable()->after('name');
            }
            if (!Schema::hasColumn('events', 'category')) {
                $table->string('category')->default('gospel')->after('slug');
            }
            if (!Schema::hasColumn('events', 'province')) {
                $table->string('province')->nullable()->after('category');
            }
            if (!Schema::hasColumn('events', 'venue_address')) {
                $table->text('venue_address')->nullable()->after('venue');
            }
            if (!Schema::hasColumn('events', 'maps_url')) {
                $table->string('maps_url', 500)->nullable()->after('venue_address');
            }
            if (!Schema::hasColumn('events', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('maps_url');
            }
            if (!Schema::hasColumn('events', 'banner_image')) {
                $table->string('banner_image')->nullable()->after('cover_image');
            }
            if (!Schema::hasColumn('events', 'logo_image')) {
                $table->string('logo_image')->nullable()->after('banner_image');
            }
            if (!Schema::hasColumn('events', 'primary_color')) {
                $table->string('primary_color', 7)->default('#D4A017')->after('logo_image');
            }
            if (!Schema::hasColumn('events', 'secondary_color')) {
                $table->string('secondary_color', 7)->default('#0D0B07')->after('primary_color');
            }
            if (!Schema::hasColumn('events', 'ticket_theme')) {
                $table->string('ticket_theme')->default('gold')->after('secondary_color');
            }
            if (!Schema::hasColumn('events', 'organizer_name')) {
                $table->string('organizer_name')->nullable()->after('ticket_theme');
            }
            if (!Schema::hasColumn('events', 'organizer_id')) {
                $table->foreignId('organizer_id')->nullable()->constrained('users')->nullOnDelete()->after('organizer_name');
            }
            if (!Schema::hasColumn('events', 'starts_at')) {
                $table->dateTime('starts_at')->nullable()->after('date');
            }
            if (!Schema::hasColumn('events', 'ends_at')) {
                $table->dateTime('ends_at')->nullable()->after('starts_at');
            }
            if (!Schema::hasColumn('events', 'gates_open_at')) {
                $table->dateTime('gates_open_at')->nullable()->after('ends_at');
            }
            if (!Schema::hasColumn('events', 'description')) {
                $table->text('description')->nullable()->after('gates_open_at');
            }
            if (!Schema::hasColumn('events', 'max_capacity')) {
                $table->integer('max_capacity')->default(0)->after('description');
            }
            if (!Schema::hasColumn('events', 'featured')) {
                $table->boolean('featured')->default(false)->after('max_capacity');
            }
            if (!Schema::hasColumn('events', 'status')) {
                $table->string('status')->default('published')->after('featured');
            }
            if (!Schema::hasColumn('events', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('status');
            }
            if (!Schema::hasColumn('events', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('events', 'event_website')) {
                $table->string('event_website')->nullable()->after('meta_description');
            }
            if (!Schema::hasColumn('events', 'event_facebook')) {
                $table->string('event_facebook')->nullable()->after('event_website');
            }
            if (!Schema::hasColumn('events', 'event_instagram')) {
                $table->string('event_instagram')->nullable()->after('event_facebook');
            }
            if (!Schema::hasColumn('events', 'contact_phone')) {
                $table->string('contact_phone')->nullable()->after('event_instagram');
            }
            if (!Schema::hasColumn('events', 'contact_email')) {
                $table->string('contact_email')->nullable()->after('contact_phone');
            }
        });

        // Gerar dados default para evento existente
        DB::table('events')->whereNull('slug')->update([
            'slug' => 'concerto-renuncia',
            'province' => 'Zambézia',
            'category' => 'gospel',
            'organizer_name' => 'Alpha Produções & Faith',
        ]);
        
        DB::statement("UPDATE events SET starts_at = CONCAT(DATE(date), ' 16:00:00') WHERE starts_at IS NULL AND date IS NOT NULL");
        DB::statement("UPDATE events SET gates_open_at = CONCAT(DATE(date), ' 08:00:00') WHERE gates_open_at IS NULL AND date IS NOT NULL");
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'organizer_id')) {
                $table->dropForeign(['organizer_id']);
            }
            $cols = array_filter([
                'slug', 'category', 'province', 'venue_address', 'maps_url',
                'cover_image', 'banner_image', 'logo_image',
                'primary_color', 'secondary_color', 'ticket_theme',
                'organizer_name', 'organizer_id',
                'starts_at', 'ends_at', 'gates_open_at',
                'description', 'max_capacity', 'featured', 'status',
                'meta_title', 'meta_description',
                'event_website', 'event_facebook', 'event_instagram',
                'contact_phone', 'contact_email',
            ], fn($col) => Schema::hasColumn('events', $col));

            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
