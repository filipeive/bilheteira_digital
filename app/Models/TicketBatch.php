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
}
