<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\TicketBatch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleEventsSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'name' => 'Concerto Renúncia',
                'slug' => 'concerto-renuncia',
                'category' => 'concerto',
                'province' => 'Zambézia',
                'city' => 'Quelimane',
                'venue' => 'Pavilhão do Benfica',
                'venue_address' => 'Av. 25 de Setembro, Quelimane',
                'starts_at' => '2026-07-11 16:00:00',
                'ends_at' => '2026-07-11 23:00:00',
                'gates_open_at' => '2026-07-11 15:00:00',
                'organizer_name' => 'Alpha Produções & Faith',
                'description' => "O Concerto Renúncia é um evento de celebração, fé e adoração profunda. Reunindo artistas renomados da música gospel moçambicana como Abel Last e Nair Nany, este encontro promete ser um marco espiritual para Quelimane e para todo o país.",
                'featured' => true,
                'status' => 'published',
                'is_active' => true,
                'ticket_theme' => 'gold',
                'contact_phone' => '87 541 1644',
                'contact_email' => 'info@ineds.org',
                'meta_title' => 'Concerto Renúncia — Quelimane 2026',
                'meta_description' => 'Compre o seu bilhete online para o Concerto Renúncia com Abel Last e Nair Nany em Quelimane.',
                'batches' => [
                    ['name' => 'Promocional', 'ticket_type' => 'promotional', 'price' => 500, 'quantity' => 200, 'sort_order' => 1],
                    ['name' => '2º Lote', 'ticket_type' => 'second_lot', 'price' => 750, 'quantity' => 300, 'sort_order' => 2],
                    ['name' => 'No Portão', 'ticket_type' => 'gate', 'price' => 1000, 'quantity' => 500, 'sort_order' => 3],
                    ['name' => 'VIP 1º Lote', 'ticket_type' => 'vip_promotional', 'price' => 1000, 'quantity' => 100, 'sort_order' => 4],
                    ['name' => 'VIP 2º Lote', 'ticket_type' => 'vip_second_lot', 'price' => 1500, 'quantity' => 100, 'sort_order' => 5],
                    ['name' => 'VIP No Portão', 'ticket_type' => 'vip', 'price' => 2000, 'quantity' => 50, 'sort_order' => 6],
                    ['name' => 'Cortesia Promotor', 'ticket_type' => 'free', 'price' => 0, 'quantity' => 50, 'sort_order' => 7],
                ]
            ],
            [
                'name' => 'Festival Adoração Moz 2026',
                'slug' => 'festival-adoracao-moz-2026',
                'category' => 'gospel',
                'province' => 'Maputo Cidade',
                'city' => 'Maputo',
                'venue' => 'Pavilhão do Maxaquene',
                'venue_address' => 'Av. Salvador Allende, Maputo',
                'starts_at' => '2026-11-28 15:00:00',
                'ends_at' => '2026-11-28 23:30:00',
                'gates_open_at' => '2026-11-28 14:00:00',
                'organizer_name' => 'Adoração Moz Events',
                'description' => "O maior festival da música gospel moçambicana! 8 horas ininterruptas de adoração, louvor e comunhão com bandas locais e convidados internacionais.",
                'featured' => true,
                'status' => 'published',
                'is_active' => true,
                'ticket_theme' => 'blue',
                'contact_phone' => '84 123 4567',
                'contact_email' => 'contacto@adoracaomoz.co.mz',
                'meta_title' => 'Festival Adoração Moz 2026 — Maputo',
                'meta_description' => 'Bilhetes para o Festival Adoração Moz 2026 no Pavilhão do Maxaquene em Maputo.',
                'batches' => [
                    ['name' => 'Geral (Promocional)', 'ticket_type' => 'promotional', 'price' => 600, 'quantity' => 500, 'sort_order' => 1],
                    ['name' => 'Geral (Normal)', 'ticket_type' => 'second_lot', 'price' => 900, 'quantity' => 1000, 'sort_order' => 2],
                    ['name' => 'Área VIP', 'ticket_type' => 'vip', 'price' => 2000, 'quantity' => 200, 'sort_order' => 3],
                    ['name' => 'Cortesia Imprensa', 'ticket_type' => 'free', 'price' => 0, 'quantity' => 30, 'sort_order' => 4],
                ]
            ],
            [
                'name' => 'Conferência Mulheres de Fé & Poder',
                'slug' => 'conferencia-mulheres-de-fe-e-poder',
                'category' => 'conferencia',
                'province' => 'Sofala',
                'city' => 'Beira',
                'venue' => 'Centro Cultural da Beira',
                'venue_address' => 'Rua Correia de Brito, Beira',
                'starts_at' => '2026-10-15 09:00:00',
                'ends_at' => '2026-10-17 18:00:00',
                'gates_open_at' => '2026-10-15 08:00:00',
                'organizer_name' => 'Ministério Mulheres Vitoriosas',
                'description' => "Três dias intensos de pregação, edificação espiritual, workshops de liderança feminina e momentos de cura e renovação na cidade da Beira.",
                'featured' => false,
                'status' => 'published',
                'is_active' => true,
                'ticket_theme' => 'purple',
                'contact_phone' => '86 987 6543',
                'contact_email' => 'mulheresdefe@gmail.com',
                'meta_title' => 'Conferência Mulheres de Fé & Poder — Beira 2026',
                'meta_description' => 'Garanta a sua inscrição para a Conferência Mulheres de Fé na Beira.',
                'batches' => [
                    ['name' => 'Inscrição Individual', 'ticket_type' => 'promotional', 'price' => 750, 'quantity' => 300, 'sort_order' => 1],
                    ['name' => 'Passe VIP + Livro', 'ticket_type' => 'vip', 'price' => 1500, 'quantity' => 80, 'sort_order' => 2],
                    ['name' => 'Cortesia Convidada', 'ticket_type' => 'free', 'price' => 0, 'quantity' => 20, 'sort_order' => 3],
                ]
            ],
            [
                'name' => 'Noite de Louvor & Glória Nampula',
                'slug' => 'noite-de-louvor-e-gloria-nampula',
                'category' => 'evangelizacao',
                'province' => 'Nampula',
                'city' => 'Nampula',
                'venue' => 'Pavilhão dos Desportos de Nampula',
                'venue_address' => 'Av. Eduardo Mondlane, Nampula',
                'starts_at' => '2026-12-05 18:00:00',
                'ends_at' => '2026-12-05 23:00:00',
                'gates_open_at' => '2026-12-05 17:00:00',
                'organizer_name' => 'União Evangélica de Nampula',
                'description' => "Grande noite de evangelização, ministração da palavra e cânticos de adoração reunindo fiéis de toda a província de Nampula.",
                'featured' => false,
                'status' => 'published',
                'is_active' => true,
                'ticket_theme' => 'green',
                'contact_phone' => '84 555 4433',
                'contact_email' => 'louvornampula@gmail.com',
                'meta_title' => 'Noite de Louvor & Glória — Nampula 2026',
                'meta_description' => 'Entrada para a Noite de Louvor & Glória no Pavilhão de Nampula.',
                'batches' => [
                    ['name' => 'Entrada Geral', 'ticket_type' => 'promotional', 'price' => 400, 'quantity' => 400, 'sort_order' => 1],
                    ['name' => 'Entrada Frontal VIP', 'ticket_type' => 'vip', 'price' => 1000, 'quantity' => 100, 'sort_order' => 2],
                    ['name' => 'Cortesia VIP', 'ticket_type' => 'free', 'price' => 0, 'quantity' => 15, 'sort_order' => 3],
                ]
            ],
            [
                'name' => 'Impacto Gospel Chimoio 2025',
                'slug' => 'impacto-gospel-chimoio-2025',
                'category' => 'concerto',
                'province' => 'Manica',
                'city' => 'Chimoio',
                'venue' => 'Auditório Municipal de Chimoio',
                'venue_address' => 'Rua Barão de Cabral, Chimoio',
                'starts_at' => '2025-11-20 17:00:00',
                'ends_at' => '2025-11-20 22:00:00',
                'gates_open_at' => '2025-11-20 16:00:00',
                'organizer_name' => 'Manica Gospel Movement',
                'description' => "Evento realizado com lotação esgotada em Novembro de 2025. Uma noite memorável que marcou a juventude de Chimoio com louvor e testemunhos extraordinários.",
                'featured' => false,
                'status' => 'ended',
                'is_active' => false,
                'ticket_theme' => 'red',
                'contact_phone' => '82 333 2211',
                'contact_email' => 'chimoio.gospel@gmail.com',
                'meta_title' => 'Impacto Gospel Chimoio 2025 (Evento Realizado)',
                'meta_description' => 'Detalhes e arquivo do Impacto Gospel Chimoio 2025.',
                'batches' => [
                    ['name' => 'Entrada Geral', 'ticket_type' => 'promotional', 'price' => 350, 'quantity' => 300, 'sort_order' => 1],
                    ['name' => 'Cortesia', 'ticket_type' => 'free', 'price' => 0, 'quantity' => 20, 'sort_order' => 2],
                ]
            ],
            [
                'name' => 'Retiro Espiritual Jovem - Chama Viva',
                'slug' => 'retiro-espiritual-jovem-chama-viva',
                'category' => 'retiro',
                'province' => 'Gaza',
                'city' => 'Xai-Xai',
                'venue' => 'Centro de Formação de Xai-Xai',
                'venue_address' => 'Praia de Xai-Xai, Gaza',
                'starts_at' => '2026-09-10 08:00:00',
                'ends_at' => '2026-09-12 16:00:00',
                'gates_open_at' => '2026-09-10 07:00:00',
                'organizer_name' => 'Juventude Batista Moçambique',
                'description' => "Três dias na praia de Xai-Xai dedicados ao estudo bíblico, fogueira de adoração, dinâmicas de grupo e renovação de propósitos espirituais.",
                'featured' => false,
                'status' => 'published',
                'is_active' => true,
                'ticket_theme' => 'gold',
                'contact_phone' => '87 444 3322',
                'contact_email' => 'chamaviva@jbm.org.mz',
                'meta_title' => 'Retiro Jovem Chama Viva — Xai-Xai 2026',
                'meta_description' => 'Inscrição para o Retiro Espiritual Jovem Chama Viva em Xai-Xai.',
                'batches' => [
                    ['name' => 'Inscrição Completa (Com Alojamento)', 'ticket_type' => 'promotional', 'price' => 1500, 'quantity' => 150, 'sort_order' => 1],
                    ['name' => 'Inscrição Simples', 'ticket_type' => 'second_lot', 'price' => 800, 'quantity' => 100, 'sort_order' => 2],
                    ['name' => 'Cortesia Líder', 'ticket_type' => 'free', 'price' => 0, 'quantity' => 10, 'sort_order' => 3],
                ]
            ],
        ];

        foreach ($events as $eventData) {
            $batches = $eventData['batches'];
            unset($eventData['batches']);

            // Manter compatibilidade com array ticket_types legado
            $ticketTypesMap = [];
            foreach ($batches as $b) {
                $ticketTypesMap[$b['ticket_type']] = [
                    'name' => $b['name'],
                    'price' => $b['price'],
                    'lot_size' => $b['quantity'],
                    'description' => $b['name'] . ' — ' . number_format($b['price'], 0, ',', '.') . ' MT',
                    'icon' => str_contains($b['ticket_type'], 'vip') ? 'star' : ($b['ticket_type'] === 'free' ? 'gift' : 'ticket'),
                    'color' => str_contains($b['ticket_type'], 'vip') ? '#D4AF37' : ($b['ticket_type'] === 'free' ? '#8B5CF6' : '#10B981'),
                ];
            }

            $eventData['ticket_types'] = $ticketTypesMap;
            $eventData['date'] = $eventData['starts_at'];
            $eventData['capacity'] = 2000;

            $event = Event::updateOrCreate(
                ['slug' => $eventData['slug']],
                $eventData
            );

            foreach ($batches as $batchData) {
                TicketBatch::updateOrCreate(
                    [
                        'event_id' => $event->id,
                        'name' => $batchData['name'],
                    ],
                    array_merge($batchData, [
                        'event_id' => $event->id,
                        'is_active' => true,
                    ])
                );
            }
        }
    }
}
