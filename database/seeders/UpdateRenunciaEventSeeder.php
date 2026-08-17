<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class UpdateRenunciaEventSeeder extends Seeder
{
    public function run(): void
    {
        // updateOrCreate / find — SEGURO para produção (não apaga nada)
        $event = Event::where('name', 'like', '%Renúncia%')
             ->orWhere('name', 'like', '%Renuncia%')
             ->first();

        if ($event) {
            $event->update([
                 'slug'            => 'concerto-renuncia',
                 'category'        => 'gospel',
                 'province'        => 'Zambézia',
                 'venue_address'   => 'Pavilhão do Benfica, Cidade de Quelimane, Zambézia',
                 'organizer_name'  => 'Alpha Produções & Faith',
                 'ticket_theme'    => 'gold',
                 'primary_color'   => '#D4A017',
                 'secondary_color' => '#0D0B07',
                 'status'          => 'published',
                 'featured'        => true,
                 'contact_phone'   => '87 541 1644',
                 'event_facebook'  => 'https://facebook.com/alphaproducoes',
                 'event_instagram' => 'https://instagram.com/alphaproducoes',
            ]);
        }
    }
}
