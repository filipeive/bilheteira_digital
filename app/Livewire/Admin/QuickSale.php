<?php

namespace App\Livewire\Admin;

use App\Models\TicketBatch;
use App\Models\Event;
use App\Models\Ticket;
use App\Services\TicketService;
use App\Services\QrCodeService;
use App\Services\AuditService;
use App\Jobs\SendTicketJob;
use Livewire\Component;

class QuickSale extends Component
{
    public int    $batchId        = 0;
    public int    $quantity       = 1;
    public string $payment_method = 'cash';
    public string $buyer_name     = '';
    public string $buyer_phone    = '';
    public string $buyer_email    = '';
    public string $notes          = '';
    public bool   $isQuickMode    = true;
    public array  $createdTickets = [];
    public bool   $showSuccess    = false;

    protected function rules(): array
    {
        return [
            'batchId'        => 'required|exists:ticket_batches,id',
            'quantity'       => 'required|integer|min:1|max:20',
            'payment_method' => 'required|in:mpesa,emola,cash,bank_transfer,other,free',
            'buyer_name'     => $this->isQuickMode ? 'nullable' : 'required|min:3',
            'buyer_phone'    => 'nullable|string',
            'buyer_email'    => 'nullable|email',
        ];
    }

    public function sale(): void
    {
        $this->validate();
        $batch = TicketBatch::findOrFail($this->batchId);

        if (!$batch->isAvailable()) {
            $this->dispatch('notify', type: 'error', message: 'Lote indisponível ou esgotado.');
            return;
        }

        if ($batch->available < $this->quantity) {
            $this->dispatch('notify', type: 'error', message: "Apenas {$batch->available} bilhetes disponíveis.");
            return;
        }

        $qrService = app(QrCodeService::class);
        $tickets = [];

        for ($i = 0; $i < $this->quantity; $i++) {
            $ticketCode = Ticket::generateCode();
            $ticket = Ticket::create([
                'ticket_code'    => $ticketCode,
                'event_id'       => $batch->event_id,
                'batch_id'       => $batch->id,
                'ticket_type'    => $batch->ticket_type,
                'price'          => $batch->price,
                'payment_method' => $this->payment_method,
                'payment_ref'    => 'PRESENCIAL-' . strtoupper(substr(uniqid(), -6)),
                'buyer_name'     => $this->buyer_name ?: "Venda Rápida #" . ($i + 1),
                'buyer_phone'    => $this->buyer_phone ?: null,
                'buyer_email'    => $this->buyer_email ?: null,
                'ticket_mode'    => $this->isQuickMode ? 'quick_sale' : 'personalized',
                'status'         => 'confirmed',
                'qr_payload'     => $qrService->generateSignedPayload(
                    (object) ['ticket_code' => $ticketCode, 'buyer_name' => $this->buyer_name ?: "Venda Rápida #" . ($i + 1), 'ticket_type' => $batch->ticket_type]
                ),
                'notes'          => $this->notes ?: null,
            ]);

            // Update QR payload with real ticket object
            $ticket->update(['qr_payload' => $qrService->generateSignedPayload($ticket)]);

            $tickets[] = ['code' => $ticket->ticket_code, 'type' => $ticket->getTicketTypeLabel()];

            if ($ticket->buyer_phone || $ticket->buyer_email) {
                SendTicketJob::dispatch($ticket, 'all')->delay(now()->addSeconds(5));
            }
        }

        $batch->increment('sold', $this->quantity);
        AuditService::log('quick_sale', null, [], [
            'batch'    => $batch->name,
            'quantity' => $this->quantity,
            'payment'  => $this->payment_method,
            'total'    => $batch->price * $this->quantity,
        ]);

        $this->createdTickets = $tickets;
        $this->showSuccess    = true;
        $qty = $this->quantity;
        $this->reset(['quantity', 'buyer_name', 'buyer_phone', 'buyer_email', 'notes']);
        $this->quantity = 1;
        $this->dispatch('notify', type: 'success', message: "{$qty} bilhete(s) gerado(s) e confirmado(s).");
    }

    public function newSale(): void
    {
        $this->showSuccess    = false;
        $this->createdTickets = [];
    }

    public function render()
    {
        $batches = TicketBatch::where('is_active', true)
            ->whereHas('event', fn($q) => $q->where('is_active', true))
            ->orderBy('sort_order')
            ->get();

        return view('livewire.admin.quick-sale', compact('batches'))
            ->layout('layouts.admin', ['title' => 'Venda Rápida']);
    }
}
