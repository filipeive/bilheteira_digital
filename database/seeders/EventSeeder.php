<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::create([
            'name' => 'Concerto Renúncia',
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
                'vip' => [
                    'name' => 'VIP',
                    'price' => 2000,
                    'lot_size' => 50,
                    'description' => 'Acesso VIP com benefícios exclusivos',
                    'icon' => 'star',
                    'color' => '#D4AF37',
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
