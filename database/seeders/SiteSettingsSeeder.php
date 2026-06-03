<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Evento
            ['key' => 'event_name',        'value' => 'Concerto Renúncia',             'group' => 'event',  'type' => 'text'],
            ['key' => 'event_date',        'value' => '2026-07-11',                    'group' => 'event',  'type' => 'text'],
            ['key' => 'event_time',        'value' => '16:00',                         'group' => 'event',  'type' => 'text'],
            ['key' => 'event_venue',       'value' => 'Pavilhão do Benfica',           'group' => 'event',  'type' => 'text'],
            ['key' => 'event_city',        'value' => 'Quelimane, Mozambique',         'group' => 'event',  'type' => 'text'],
            ['key' => 'event_description', 'value' => 'Abel Last & Nair Nany em Concerto', 'group' => 'event', 'type' => 'text'],
            ['key' => 'event_contact_1',   'value' => '87 541 1644',                   'group' => 'event',  'type' => 'text'],
            ['key' => 'event_contact_2',   'value' => '84 887 1940',                   'group' => 'event',  'type' => 'text'],
            // Social
            ['key' => 'social_facebook',   'value' => 'https://facebook.com/alphaproducoes',  'group' => 'social', 'type' => 'text'],
            ['key' => 'social_instagram',  'value' => 'https://instagram.com/alphaproducoes', 'group' => 'social', 'type' => 'text'],
            ['key' => 'social_tiktok',     'value' => 'https://tiktok.com/@alphaproducoes',   'group' => 'social', 'type' => 'text'],
            ['key' => 'social_whatsapp',   'value' => 'https://wa.me/258875411644',           'group' => 'social', 'type' => 'text'],
            // Banner
            ['key' => 'banner_title',      'value' => 'RENÚNCIA',                      'group' => 'banner', 'type' => 'text'],
            ['key' => 'banner_subtitle',   'value' => 'Abel Last & Nair Nany em Concerto', 'group' => 'banner', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
