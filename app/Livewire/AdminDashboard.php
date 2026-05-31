<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Ticket;
use App\Services\TicketService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AdminDashboard extends Component
{
    public ?int $eventId = null;

    public function mount(): void
    {
        $event = Event::where('is_active', true)->first();
        $this->eventId = $event?->id;
    }

    #[Computed]
    public function event(): ?Event
    {
        return $this->eventId ? Event::find($this->eventId) : null;
    }

    #[Computed]
    public function stats(): array
    {
        if (!$this->event) {
            return [
                'total' => 0, 'confirmed' => 0, 'pending' => 0,
                'used' => 0, 'cancelled' => 0, 'revenue' => 0,
                'by_type' => [],
            ];
        }

        return app(TicketService::class)->getEventStats($this->event);
    }

    #[Computed]
    public function salesByDay(): array
    {
        if (!$this->event) return [];

        return Ticket::where('event_id', $this->eventId)
            ->whereIn('status', ['confirmed', 'used'])
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(price) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($row) => [
                'date' => \Carbon\Carbon::parse($row->date)->format('d/m'),
                'count' => $row->count,
                'revenue' => $row->revenue,
            ])
            ->toArray();
    }

    #[Computed]
    public function recentTickets(): \Illuminate\Database\Eloquent\Collection
    {
        return Ticket::where('event_id', $this->eventId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin-dashboard')
            ->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}
