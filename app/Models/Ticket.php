<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

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
        // Phase 2 additions
        'email_sent_at',
        'whatsapp_sent_at',
        'reminder_sent_at',
        'ticket_mode',
        'batch_id',
        'scanned_device',
    ];

    protected function casts(): array
    {
        return [
            'price'            => 'integer',
            'used_at'          => 'datetime',
            'email_sent_at'    => 'datetime',
            'whatsapp_sent_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
        ];
    }

    // ── Relations ────────────────────────────────────────────

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(TicketBatch::class, 'batch_id');
    }

    // ── Status checks ────────────────────────────────────────

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

    // ── Notification helpers ─────────────────────────────────

    public function wasEmailSent(): bool
    {
        return !is_null($this->email_sent_at);
    }

    public function wasWhatsAppSent(): bool
    {
        return !is_null($this->whatsapp_sent_at);
    }

    public function isQuickSale(): bool
    {
        return $this->ticket_mode === 'quick_sale';
    }

    // ── Actions ──────────────────────────────────────────────

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

        try {
            \App\Models\SystemNotification::add(
                title: 'Bilhete Validado',
                message: "O bilhete {$this->ticket_code} ({$this->buyer_name}) foi validado por {$user->name}.",
                type: 'success',
                link: '/admin/tickets'
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to create scan system notification: " . $e->getMessage());
        }

        return true;
    }

    public static function getEventPrefix(?Event $event = null): string
    {
        if (!$event || empty($event->name)) {
            return 'EVT';
        }

        $name = trim($event->name);
        $words = preg_split('/\s+/', $name);
        $words = array_values(array_filter($words, fn($w) => !in_array(strtolower($w), ['de', 'do', 'da', 'dos', 'das', '&', 'e'])));

        if (count($words) >= 3) {
            $prefix = mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1) . mb_substr($words[2], 0, 1);
        } elseif (count($words) === 2) {
            $prefix = mb_substr($words[0], 0, 2) . mb_substr($words[1], 0, 1);
        } else {
            $prefix = mb_substr($name, 0, 3);
        }

        $prefix = strtoupper(preg_replace('/[^a-zA-Z]/', '', $prefix));
        if (strlen($prefix) < 2) {
            $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name) . 'EVT', 0, 3));
        }

        return Str::limit($prefix, 3, '');
    }

    public static function generateCode(?Event $event = null): string
    {
        $prefix = static::getEventPrefix($event);
        do {
            $code = $prefix . '-' . strtoupper(Str::random(6));
        } while (static::where('ticket_code', $code)->exists());

        return $code;
    }

    // ── Label helpers ────────────────────────────────────────

    public function getTicketTypeLabel(): string
    {
        return match ($this->ticket_type) {
            'promotional' => 'Promocional',
            'second_lot' => '2º Lote',
            'gate' => 'No Portão',
            'vip_promotional' => 'VIP 1º Lote',
            'vip_second_lot' => 'VIP 2º Lote',
            'vip' => 'VIP No Portão',
            'free' => 'Gratuito',
            'child' => 'Criança',
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
