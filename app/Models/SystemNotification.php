<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'type',
        'is_read',
        'link',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Helper to create a notification easily.
     */
    public static function add(string $title, string $message, string $type = 'info', ?string $link = null): self
    {
        return self::create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'is_read' => false,
        ]);
    }
}
