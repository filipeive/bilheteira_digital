<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'venue',
        'city',
        'capacity',
        'ticket_types',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'ticket_types' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function getTicketTypePrices(): array
    {
        return $this->ticket_types ?? [];
    }

    public function getTotalRevenue(): int
    {
        return $this->tickets()
            ->whereIn('status', ['confirmed', 'used'])
            ->sum('price');
    }
}
