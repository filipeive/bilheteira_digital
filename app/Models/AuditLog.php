<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'old_values', 'new_values', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedChanges(): array
    {
        $changes = [];
        $old = $this->old_values ?: [];
        $new = $this->new_values ?: [];

        if ($this->action === 'ticket_deleted') {
            $code = $old['ticket_code'] ?? '—';
            $name = $old['buyer_name'] ?? '—';
            $type = $old['ticket_type'] ?? '—';
            $price = isset($old['price']) ? number_format($old['price'], 0, ',', '.') . ' MT' : '—';
            return ["O bilhete <strong>{$code}</strong> (Tipo: {$type}, Titular: {$name}, Preço: {$price}) foi <strong>ELIMINADO permanentemente</strong>."];
        }

        $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));
        
        $labels = [
            'buyer_name' => 'Nome do Titular',
            'buyer_phone' => 'Telefone',
            'buyer_email' => 'Email',
            'status' => 'Estado do Bilhete',
            'used_at' => 'Data de Validação',
            'scanned_by' => 'Validado por',
            'scanned_device' => 'Dispositivo do Scanner',
            'price' => 'Preço',
            'ticket_type' => 'Tipo de Bilhete',
            'notes' => 'Notas',
            'is_active' => 'Ativo',
            'avatar_url' => 'Avatar',
            'role' => 'Função',
            'email' => 'E-mail',
            'name' => 'Nome',
        ];

        foreach ($allKeys as $key) {
            if (in_array($key, ['id', 'created_at', 'updated_at', 'event_id', 'qr_payload'])) {
                continue;
            }

            $oldVal = $old[$key] ?? null;
            $newVal = $new[$key] ?? null;

            if ($oldVal !== $newVal) {
                $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                
                $formatValue = function ($val, $k) {
                    if ($val === null) return 'Nulo';
                    
                    if (in_array($k, ['scanned_by', 'user_id']) && is_numeric($val)) {
                        $usr = \App\Models\User::find($val);
                        return $usr ? $usr->name : "ID {$val}";
                    }

                    if ($val === true || $val === 1 || $val === '1') return 'Sim';
                    if ($val === false || $val === 0 || $val === '0') return 'Não';
                    
                    if ($k === 'status') {
                        $statuses = [
                            'pending' => 'Pendente',
                            'confirmed' => 'Confirmado',
                            'used' => 'Usado',
                            'cancelled' => 'Cancelado',
                        ];
                        return $statuses[$val] ?? $val;
                    }
                    
                    return (string)$val;
                };

                $oldStr = $formatValue($oldVal, $key);
                $newStr = $formatValue($newVal, $key);
                
                $changes[] = "<strong>{$label}</strong>: <span style='color: #EF4444; text-decoration: line-through;'>{$oldStr}</span> ➔ <span style='color: #10B981; font-weight: 600;'>{$newStr}</span>";
            }
        }

        if (empty($changes)) {
            if ($this->action === 'ticket_created') {
                $code = $new['ticket_code'] ?? '—';
                $name = $new['buyer_name'] ?? '—';
                return ["Bilhete <strong>{$code}</strong> criado para {$name}."];
            }
            if ($this->action === 'ticket_validated') {
                return ["Entrada validada com sucesso via scanner."];
            }
            if ($this->action === 'ticket_confirmed') {
                return ["Bilhete confirmado manualmente pelo administrador."];
            }
            if ($this->action === 'ticket_cancelled') {
                return ["Bilhete cancelado manualmente pelo administrador."];
            }
        }

        return $changes;
    }
}
