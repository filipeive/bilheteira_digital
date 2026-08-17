<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
        // Enriched multi-event fields
        'slug',
        'category',
        'province',
        'venue_address',
        'maps_url',
        'cover_image',
        'banner_image',
        'logo_image',
        'primary_color',
        'secondary_color',
        'ticket_theme',
        'organizer_name',
        'organizer_id',
        'starts_at',
        'ends_at',
        'gates_open_at',
        'description',
        'max_capacity',
        'featured',
        'status',
        'meta_title',
        'meta_description',
        'event_website',
        'event_facebook',
        'event_instagram',
        'contact_phone',
        'contact_email',
    ];

    protected function casts(): array
    {
        return [
            'date'          => 'datetime',
            'starts_at'     => 'datetime',
            'ends_at'       => 'datetime',
            'gates_open_at' => 'datetime',
            'ticket_types'  => 'array',
            'is_active'     => 'boolean',
            'featured'      => 'boolean',
        ];
    }

    const CATEGORIES = [
        'gospel'        => 'Gospel',
        'evangelizacao' => 'Evangelização',
        'conferencia'   => 'Conferência',
        'concerto'      => 'Concerto',
        'encontro'      => 'Encontro',
        'retiro'        => 'Retiro',
        'formacao'      => 'Formação',
        'culto'         => 'Culto Especial',
    ];

    const PROVINCES = [
        'Maputo Cidade'  => 'Maputo Cidade',
        'Maputo'         => 'Maputo Província',
        'Gaza'           => 'Gaza',
        'Inhambane'      => 'Inhambane',
        'Sofala'         => 'Sofala',
        'Manica'         => 'Manica',
        'Tete'           => 'Tete',
        'Zambézia'       => 'Zambézia',
        'Nampula'        => 'Nampula',
        'Cabo Delgado'   => 'Cabo Delgado',
        'Niassa'         => 'Niassa',
    ];

    const THEMES = [
        'gold'   => ['primary' => '#D4A017', 'bg' => '#0D0B07', 'label' => 'Dourado Clássico'],
        'blue'   => ['primary' => '#4A9EE0', 'bg' => '#0A1628', 'label' => 'Azul Real'],
        'purple' => ['primary' => '#9B59B6', 'bg' => '#1A0A28', 'label' => 'Púrpura'],
        'green'  => ['primary' => '#27AE60', 'bg' => '#0A1F0A', 'label' => 'Verde Esperança'],
        'red'    => ['primary' => '#E74C3C', 'bg' => '#1A0A0A', 'label' => 'Vermelho Fogo'],
        'custom' => ['primary' => null,      'bg' => null,      'label' => 'Personalizado'],
    ];

    const STATUS = [
        'draft'     => 'Rascunho',
        'published' => 'Publicado',
        'cancelled' => 'Cancelado',
        'ended'     => 'Encerrado',
        'sold_out'  => 'Esgotado',
    ];

    // ── Relations ────────────────────────────────────────────

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(TicketBatch::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    // ── Accessors & Methods ─────────────────────────────────

    public function getThemeColorsAttribute(): array
    {
        if ($this->ticket_theme === 'custom') {
            return [
                'primary' => $this->primary_color ?? '#D4A017',
                'bg'      => $this->secondary_color ?? '#0D0B07',
            ];
        }
        return self::THEMES[$this->ticket_theme] ?? self::THEMES['gold'];
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image
            ? asset('storage/' . $this->cover_image)
            : null;
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_image
            ? asset('storage/' . $this->logo_image)
            : asset('alpha-logo-gold.png');
    }

    public function getPublicUrlAttribute(): string
    {
        return url('/eventos/' . ($this->slug ?? $this->id));
    }

    public function getSaleUrlAttribute(): string
    {
        return url('/eventos/' . ($this->slug ?? $this->id) . '/bilhetes');
    }

    public function isUpcoming(): bool
    {
        $date = $this->starts_at ?? $this->date;
        return $date && $date->isFuture();
    }

    public function isOngoing(): bool
    {
        $start = $this->starts_at ?? $this->date;
        if (!$start || !$this->ends_at) return false;
        return now()->between($start, $this->ends_at);
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

    // Boot: auto-gerar slug
    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->name);
            }
        });
    }
}
