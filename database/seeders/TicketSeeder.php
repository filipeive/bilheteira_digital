<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Ticket;
use App\Services\QrCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $event = Event::where('is_active', true)->first();
        $qrService = app(QrCodeService::class);

        $buyers = [
            ['name' => 'João Mateus', 'phone' => '+258841234567'],
            ['name' => 'Maria Saide', 'phone' => '+258842345678'],
            ['name' => 'Carlos Nhantumbo', 'phone' => '+258843456789'],
            ['name' => 'Ana Bela Macamo', 'phone' => '+258844567890'],
            ['name' => 'Pedro Zacarias', 'phone' => '+258845678901'],
            ['name' => 'Fátima Amade', 'phone' => '+258846789012'],
            ['name' => 'Roberto Nkavandame', 'phone' => '+258847890123'],
            ['name' => 'Luísa Mondlane', 'phone' => '+258848901234'],
            ['name' => 'Alberto Chitima', 'phone' => '+258849012345'],
            ['name' => 'Sofia Tembe', 'phone' => '+258850123456'],
            ['name' => 'Mário Baloi', 'phone' => '+258851234567'],
            ['name' => 'Cláudia Cossa', 'phone' => '+258852345678'],
            ['name' => 'Fernando Sitoe', 'phone' => '+258853456789'],
            ['name' => 'Graça Mutemba', 'phone' => '+258854567890'],
            ['name' => 'Nelson Chissano', 'phone' => '+258855678901'],
            ['name' => 'Beatriz Mabjaia', 'phone' => '+258856789012'],
            ['name' => 'Armando Guebuza', 'phone' => '+258857890123'],
            ['name' => 'Teresa Machel', 'phone' => '+258858901234'],
            ['name' => 'Daniel Matsimbe', 'phone' => '+258859012345'],
            ['name' => 'Rosa Dimande', 'phone' => '+258860123456'],
        ];

        $types = ['promotional', 'promotional', 'second_lot', 'second_lot', 'vip'];
        $statuses = ['confirmed', 'confirmed', 'confirmed', 'pending', 'used'];
        $methods = ['mpesa', 'mpesa', 'emola', 'cash', 'mpesa'];

        $prices = [
            'promotional' => 500,
            'second_lot' => 750,
            'gate' => 1000,
            'vip' => 2000,
            'free' => 0,
        ];

        foreach ($buyers as $i => $buyer) {
            $type = $types[$i % count($types)];
            $status = $statuses[$i % count($statuses)];
            $method = $methods[$i % count($methods)];

            $ticket = new Ticket([
                'event_id' => $event->id,
                'buyer_name' => $buyer['name'],
                'buyer_phone' => $buyer['phone'],
                'buyer_email' => null,
                'ticket_type' => $type,
                'price' => $prices[$type],
                'payment_ref' => 'REF-' . strtoupper(Str::random(8)),
                'payment_method' => $type === 'free' ? 'free' : $method,
                'status' => $status,
                'notes' => null,
            ]);

            $ticket->id = (string) Str::uuid();
            $ticket->ticket_code = Ticket::generateCode();
            $ticket->qr_payload = $qrService->generateSignedPayload($ticket);

            if ($status === 'used') {
                $ticket->used_at = now()->subHours(rand(1, 48));
            }

            $ticket->save();
        }
    }
}
