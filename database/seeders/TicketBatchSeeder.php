<?php

namespace Database\Seeders;

use App\Models\TicketBatch;
use App\Models\Event;
use Illuminate\Database\Seeder;

class TicketBatchSeeder extends Seeder
{
    public function run(): void
    {
        $event = Event::where('is_active', true)->first();
        if (!$event) return;

        $batches = [
            ['name' => 'Bilhete Promocional', 'ticket_type' => 'promotional', 'price' => 500,  'quantity' => 200, 'sort_order' => 1],
            ['name' => 'Segundo Lote',        'ticket_type' => 'second_lot',  'price' => 750,  'quantity' => 300, 'sort_order' => 2],
            ['name' => 'No Portão',           'ticket_type' => 'gate',        'price' => 1000, 'quantity' => 9999, 'sort_order' => 3],
            ['name' => 'VIP',                 'ticket_type' => 'vip',         'price' => 2000, 'quantity' => 50,  'sort_order' => 4],
            ['name' => 'Cortesia',            'ticket_type' => 'free',        'price' => 0,    'quantity' => 50,  'sort_order' => 5],
        ];

        foreach ($batches as $batch) {
            TicketBatch::updateOrCreate(
                ['name' => $batch['name'], 'event_id' => $event->id],
                array_merge($batch, ['event_id' => $event->id, 'is_active' => true])
            );
        }
    }
}
