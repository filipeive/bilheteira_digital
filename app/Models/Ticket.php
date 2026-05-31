<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'ticket_code',
        'event_id',
        'buyer_name',
        'buyer_phone',
        'buyer_email',
        'ticket_type',
        'price',
        'payment_ref',
        'payment_method',
        'status',
        'qr_payload',
        'used_at',
        'scanned_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'used_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public function isUsed(): bool
    {
        return $this->status === 'used';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function markAsUsed(User $user): bool
    {
        if ($this->isUsed()) {
            return false;
        }

        $this->update([
            'status' => 'used',
            'used_at' => now(),
            'scanned_by' => $user->id,
        ]);

        return true;
    }

    public static function generateCode(): string
    {
        do {
            $code = 'REN-' . strtoupper(Str::random(6));
        } while (static::where('ticket_code', $code)->exists());

        return $code;
    }

    public function getTicketTypeLabel(): string
    {
        return match ($this->ticket_type) {
            'promotional' => 'Promocional',
            'second_lot' => '2º Lote',
            'gate' => 'No Portão',
            'vip' => 'VIP',
            'free' => 'Gratuito',
            default => $this->ticket_type,
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pendente',
            'confirmed' => 'Confirmado',
            'used' => 'Usado',
            'cancelled' => 'Cancelado',
            default => $this->status,
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'green',
            'used' => 'blue',
            'cancelled' => 'red',
            default => 'gray',
        };
    }
}
