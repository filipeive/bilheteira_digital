<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::updateOrCreate(
            ['name' => 'Concerto Renúncia'],
            [
            'date' => '2026-07-11 18:00:00',
            'venue' => 'Pavilhão do Benfica',
            'city' => 'Quelimane',
            'capacity' => 2000,
            'is_active' => true,
            'ticket_types' => [
                'promotional' => [
                    'name' => 'Promocional',
                    'price' => 500,
                    'lot_size' => 200,
                    'description' => '1º Lote — Preço especial de lançamento',
                    'icon' => 'ticket',
                    'color' => '#10B981',
                ],
                'second_lot' => [
                    'name' => '2º Lote',
                    'price' => 750,
                    'lot_size' => 300,
                    'description' => '2º Lote — Preço normal antecipado',
                    'icon' => 'ticket',
                    'color' => '#3B82F6',
                ],
                'gate' => [
                    'name' => 'No Portão',
                    'price' => 1000,
                    'lot_size' => 0,
                    'description' => 'Compra no dia do evento',
                    'icon' => 'door-open',
                    'color' => '#F59E0B',
                ],
                'vip_promotional' => [
                    'name' => 'VIP 1º Lote',
                    'price' => 1000,
                    'lot_size' => 100,
                    'description' => 'Acesso VIP — Preço promocional',
                    'icon' => 'star',
                    'color' => '#D4AF37',
                ],
                'vip_second_lot' => [
                    'name' => 'VIP 2º Lote',
                    'price' => 1500,
                    'lot_size' => 100,
                    'description' => 'Acesso VIP — 2º Lote',
                    'icon' => 'star',
                    'color' => '#FBBF24',
                ],
                'vip' => [
                    'name' => 'VIP No Portão',
                    'price' => 2000,
                    'lot_size' => 50,
                    'description' => 'Acesso VIP — Compra na porta',
                    'icon' => 'star',
                    'color' => '#B45309',
                ],
                'free' => [
                    'name' => 'Gratuito',
                    'price' => 0,
                    'lot_size' => 0,
                    'description' => 'Cortesia — Apenas via admin',
                    'icon' => 'gift',
                    'color' => '#8B5CF6',
                ],
            ],
        ]);
    }
}
