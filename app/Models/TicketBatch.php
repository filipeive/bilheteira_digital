<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketBatch extends Model
{
    protected $fillable = [
        'name', 'description', 'ticket_type', 'price',
        'quantity', 'sold', 'starts_at', 'ends_at',
        'is_active', 'sort_order', 'event_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'is_active' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'batch_id');
    }

    public function getAvailableAttribute(): int
    {
        return max(0, $this->quantity - $this->sold);
    }

    public function isAvailable(): bool
    {
        if (!$this->is_active) return false;
        if ($this->available <= 0) return false;
        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->ends_at && now()->gt($this->ends_at)) return false;
        return true;
    }

    public function getPercentageSoldAttribute(): int
    {
        if ($this->quantity === 0) return 0;
        return (int) (($this->sold / $this->quantity) * 100);
    }

    public function getDisplayNameAttribute(): string
    {
        $raw = strtolower($this->name);

        $suffixes = ['_tickets', '-tickets', 'tickets', '_ticket', '-ticket', 'ticket', '_bilhetes', '-bilhetes', 'bilhetes'];
        foreach ($suffixes as $suffix) {
            if (str_ends_with($raw, $suffix)) {
                $raw = substr($raw, 0, -strlen($suffix));
                break;
            }
        }

        $map = [
            'first_phase' => 'Primeiro Lote',
            'first_lot' => 'Primeiro Lote',
            'second_phase' => 'Segundo Lote',
            'second_lot' => 'Segundo Lote',
            'promotional' => 'Promocional',
            'vip' => 'VIP',
            'vip_promotional' => 'VIP Promocional',
            'vip_second_lot' => 'VIP 2º Lote',
            'gate' => 'No Portão',
            'free' => 'Gratuito',
            'child' => 'Criança',
        ];

        if (isset($map[$raw])) {
            return $map[$raw];
        }

        return ucwords(str_replace(['_', '-'], ' ', $raw));
    }
}
