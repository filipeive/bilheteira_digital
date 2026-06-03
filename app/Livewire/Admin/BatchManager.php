<?php

namespace App\Livewire\Admin;

use App\Models\TicketBatch;
use App\Models\Event;
use App\Services\AuditService;
use Livewire\Component;

class BatchManager extends Component
{
    public int    $eventId     = 0;
    public string $name        = '';
    public string $description = '';
    public string $ticket_type = 'promotional';
    public int    $price       = 0;
    public int    $quantity    = 100;
    public string $starts_at   = '';
    public string $ends_at     = '';
    public bool   $is_active   = true;
    public int    $sort_order  = 0;
    public ?int   $editingId   = null;

    protected $rules = [
        'name'        => 'required|min:3|max:100',
        'ticket_type' => 'required|string',
        'price'       => 'required|integer|min:0',
        'quantity'    => 'required|integer|min:1',
        'starts_at'   => 'nullable|date',
        'ends_at'     => 'nullable|date|after_or_equal:starts_at',
        'is_active'   => 'boolean',
        'sort_order'  => 'integer|min:0',
    ];

    public function mount(): void
    {
        $event = Event::where('is_active', true)->first();
        $this->eventId = $event?->id ?? 0;
    }

    public function save(): void
    {
        $this->validate();
        $data = [
            'name'        => $this->name,
            'description' => $this->description ?: null,
            'ticket_type' => $this->ticket_type,
            'price'       => $this->price,
            'quantity'    => $this->quantity,
            'starts_at'   => $this->starts_at ?: null,
            'ends_at'     => $this->ends_at ?: null,
            'is_active'   => $this->is_active,
            'sort_order'  => $this->sort_order,
            'event_id'    => $this->eventId,
        ];

        if ($this->editingId) {
            $batch = TicketBatch::findOrFail($this->editingId);
            $batch->update($data);
            AuditService::log('updated_batch', $batch);
            $this->dispatch('notify', type: 'success', message: 'Lote actualizado.');
        } else {
            $batch = TicketBatch::create($data);
            AuditService::log('created_batch', $batch);
            $this->dispatch('notify', type: 'success', message: 'Lote criado.');
        }
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $batch = TicketBatch::findOrFail($id);
        $this->editingId   = $id;
        $this->name        = $batch->name;
        $this->description = $batch->description ?? '';
        $this->ticket_type = $batch->ticket_type;
        $this->price       = $batch->price;
        $this->quantity    = $batch->quantity;
        $this->is_active   = $batch->is_active;
        $this->sort_order  = $batch->sort_order;
        $this->starts_at   = $batch->starts_at?->format('Y-m-d\TH:i') ?? '';
        $this->ends_at     = $batch->ends_at?->format('Y-m-d\TH:i') ?? '';
    }

    public function toggleActive(int $id): void
    {
        $batch = TicketBatch::findOrFail($id);
        $batch->update(['is_active' => !$batch->is_active]);
        AuditService::log('toggled_batch_status', $batch);
        $this->dispatch('notify', type: 'success', message: 'Estado do lote alterado.');
    }

    public function delete(int $id): void
    {
        $batch = TicketBatch::findOrFail($id);
        AuditService::log('deleted_batch', $batch);
        $batch->delete();
        $this->dispatch('notify', type: 'success', message: 'Lote eliminado.');
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'description', 'price', 'quantity', 'starts_at', 'ends_at', 'editingId']);
        $this->ticket_type = 'promotional';
        $this->is_active   = true;
        $this->sort_order  = 0;
    }

    public function render()
    {
        return view('livewire.admin.batch-manager', [
            'batches' => TicketBatch::where('event_id', $this->eventId)->orderBy('sort_order')->get(),
        ])->layout('layouts.admin', ['title' => 'Gestão de Lotes']);
    }
}
