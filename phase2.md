# PROMPT — Fase 2: Evolução do Sistema de Bilhetagem
# Concerto Renúncia 2026 · Laravel 11 · Livewire 3
# ════════════════════════════════════════════════════

## CONTEXTO CRÍTICO — LER ANTES DE QUALQUER COISA

O sistema já está em produção em https://ineds.org/alpha/bilhetes
A infraestrutura existente está FUNCIONAL e não deve ser quebrada.

### O que JÁ EXISTE e deve ser PRESERVADO:

```
Models:     Event, Ticket, User (com role: admin/operator/organizer)
Services:   TicketService, QrCodeService, MpesaService
Controllers: PublicTicketController, AdminController, ValidationController
Livewire:   TicketForm, AdminDashboard, TicketList, ManualTicketForm
Jobs:       SendTicketJob, TicketMail
Middleware: CheckRole (admin, operator, organizer)
Auth:       Laravel Breeze (sessão) + Sanctum (API)
DB:         fili3528_larav75 (MySQL) — migrations já corridas
Hosting:    HostGator cPanel · PHP 8.3 · Queue driver: database
URL:        https://ineds.org/alpha/bilhetes
```

### Regras obrigatórias:
1. NUNCA correr `migrate:fresh` — os dados de produção existem
2. SEMPRE criar novas migrations aditivas (`add_*`, `create_*`)
3. NUNCA renomear tabelas ou colunas existentes
4. NUNCA mudar nomes de rotas existentes sem redirects
5. PRESERVAR o design system: #0D0B07 bg, #D4A017 gold, Bebas Neue + Montserrat + JetBrains Mono
6. PRESERVAR o URL base /alpha/bilhetes em todos os asset() e route()
7. Queue driver é `database` — sem Redis, sem Supervisor

---

## ANÁLISE DO DOCUMENTO DE MELHORIAS

As 14 áreas propostas foram avaliadas e reorganizadas em:

- ✅ IMPLEMENTAR — alinhado com o sistema actual
- ⚠ ADAPTAR — boa ideia mas precisa de ajuste para não quebrar o que existe
- ❌ ADIAR — complexidade desnecessária para o evento de Julho 2026

---

## FASE 2A — MIGRATIONS ADITIVAS (correr primeiro)

```bash
php artisan make:migration add_notification_columns_to_tickets_table
php artisan make:migration add_profile_columns_to_users_table
php artisan make:migration create_audit_logs_table
php artisan make:migration create_site_settings_table
php artisan make:migration create_ticket_batches_table
php artisan make:migration add_batch_id_to_tickets_table
php artisan make:migration add_ticket_mode_to_tickets_table
```

### add_notification_columns_to_tickets_table
```php
Schema::table('tickets', function (Blueprint $table) {
    $table->timestamp('email_sent_at')->nullable()->after('status');
    $table->timestamp('whatsapp_sent_at')->nullable()->after('email_sent_at');
    $table->timestamp('reminder_sent_at')->nullable()->after('whatsapp_sent_at');
    $table->string('ticket_mode')->default('personalized')->after('reminder_sent_at');
    // ticket_mode: 'personalized' | 'quick_sale'
    $table->unsignedBigInteger('batch_id')->nullable()->after('ticket_mode');
    $table->string('scanned_device')->nullable()->after('scanned_by');
});
```

### add_profile_columns_to_users_table
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('phone')->nullable()->after('email');
    $table->string('avatar')->nullable()->after('phone');
    $table->boolean('is_active')->default(true)->after('avatar');
    // Expandir role para suportar novos papéis futuros
    // NÃO mudar o tipo — adicionar valores ao enum via string
    // Ou melhor: mudar para string simples para flexibilidade
    $table->string('role')->default('operator')->change();
    // Roles válidos: super_admin, admin, organizer, operator, scanner, seller
});
```

### create_audit_logs_table
```php
Schema::create('audit_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    $table->string('action');           // created_ticket, confirmed_ticket, etc.
    $table->string('model_type')->nullable();
    $table->string('model_id')->nullable();
    $table->json('old_values')->nullable();
    $table->json('new_values')->nullable();
    $table->string('ip_address')->nullable();
    $table->text('user_agent')->nullable();
    $table->timestamps();
});
```

### create_site_settings_table
```php
Schema::create('site_settings', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique();
    $table->text('value')->nullable();
    $table->string('type')->default('text'); // text, json, boolean, image
    $table->string('group')->default('general'); // general, event, social, faq
    $table->timestamps();
});
```

### create_ticket_batches_table
```php
Schema::create('ticket_batches', function (Blueprint $table) {
    $table->id();
    $table->string('name');              // "Lote Promocional", "VIP Especial"
    $table->text('description')->nullable();
    $table->string('ticket_type');       // tipo base
    $table->integer('price');            // preço em MZN
    $table->integer('quantity');         // total disponível
    $table->integer('sold')->default(0); // vendidos
    $table->datetime('starts_at')->nullable();
    $table->datetime('ends_at')->nullable();
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->foreignId('event_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
});
```

Após criar as migrations:
```bash
php artisan migrate --force
```

---

## FASE 2B — NOVOS MODELS E UPDATES

### app/Models/AuditLog.php (NOVO)
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'old_values', 'new_values', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### app/Models/SiteSetting.php (NOVO)
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group'];

    /**
     * Obter valor de uma configuração com cache.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting?->value ?? $default;
        });
    }

    /**
     * Definir valor e limpar cache.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }

    /**
     * Obter grupo completo de configurações.
     */
    public static function group(string $group): array
    {
        return Cache::remember("settings_group_{$group}", 3600, function () use ($group) {
            return static::where('group', $group)->pluck('value', 'key')->toArray();
        });
    }
}
```

### app/Models/TicketBatch.php (NOVO)
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketBatch extends Model
{
    protected $fillable = [
        'name', 'description', 'ticket_type', 'price',
        'quantity', 'sold', 'starts_at', 'ends_at',
        'is_active', 'sort_order', 'event_id'
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
```

### Actualizar app/Models/User.php
Adicionar ao modelo existente (NÃO substituir):
```php
// Adicionar ao $fillable existente:
'phone', 'avatar', 'is_active',

// Adicionar constante de roles válidos:
const ROLES = [
    'super_admin' => 'Super Admin',
    'admin'       => 'Administrador',
    'organizer'   => 'Organizador',
    'operator'    => 'Operador',
    'scanner'     => 'Scanner',
    'seller'      => 'Vendedor',
];

// Adicionar métodos:
public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
public function isScanner(): bool    { return $this->role === 'scanner'; }
public function isSeller(): bool     { return $this->role === 'seller'; }
public function canAccessAdmin(): bool
{
    return in_array($this->role, ['super_admin', 'admin', 'organizer']);
}
public function canValidate(): bool
{
    return in_array($this->role, ['super_admin', 'admin', 'operator', 'scanner']);
}
public function canSell(): bool
{
    return in_array($this->role, ['super_admin', 'admin', 'organizer', 'seller']);
}
public function getAvatarUrlAttribute(): string
{
    return $this->avatar
        ? asset('storage/' . $this->avatar)
        : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=D4A017&color=0D0B07&length=2';
}
```

### Actualizar app/Models/Ticket.php
Adicionar ao modelo existente:
```php
// Adicionar ao $fillable:
'email_sent_at', 'whatsapp_sent_at', 'reminder_sent_at',
'ticket_mode', 'batch_id', 'scanned_device',

// Adicionar ao $casts:
'email_sent_at'     => 'datetime',
'whatsapp_sent_at'  => 'datetime',
'reminder_sent_at'  => 'datetime',

// Adicionar relação:
public function batch()
{
    return $this->belongsTo(TicketBatch::class, 'batch_id');
}

// Helpers de notificação:
public function wasEmailSent(): bool    { return !is_null($this->email_sent_at); }
public function wasWhatsAppSent(): bool { return !is_null($this->whatsapp_sent_at); }
public function isQuickSale(): bool     { return $this->ticket_mode === 'quick_sale'; }
```

---

## FASE 2C — SERVIÇOS NOVOS

### app/Services/EmailService.php (NOVO — ver prompt-notificacoes.md)

Implementar conforme especificado no prompt de notificações anterior.
Resumo:
- `sendTicketConfirmation(Ticket $ticket): bool`
- `sendPaymentPending(Ticket $ticket): bool`
- Usa `TicketConfirmationMail` e `PaymentPendingMail`
- Log de sucesso/falha
- Actualiza `email_sent_at` após envio

### app/Services/WhatsAppService.php (NOVO — ver prompt-notificacoes.md)

Implementar conforme especificado.
Resumo:
- `sendTicketConfirmation(Ticket $ticket): bool`
- `sendPaymentPending(Ticket $ticket): bool`
- `sendEventReminder(Ticket $ticket): bool`
- `sendText(string $phone, string $message): bool`
- Formatação automática +258
- Actualiza `whatsapp_sent_at` após envio

### app/Services/AuditService.php (NOVO)
```php
<?php
namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public static function log(
        string $action,
        mixed  $model = null,
        array  $oldValues = [],
        array  $newValues = []
    ): void {
        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id'   => $model?->getKey(),
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
```

Usar em pontos críticos:
```php
// Quando confirmar bilhete:
AuditService::log('confirmed_ticket', $ticket, ['status' => 'pending'], ['status' => 'confirmed']);

// Quando cancelar:
AuditService::log('cancelled_ticket', $ticket);

// Quando criar utilizador:
AuditService::log('created_user', $user, [], $user->toArray());
```

---

## FASE 2D — ACTUALIZAR JOB DE ENVIO

### Actualizar app/Jobs/SendTicketJob.php

Substituir o conteúdo actual por:

```php
<?php
namespace App\Jobs;

use App\Models\Ticket;
use App\Services\EmailService;
use App\Services\WhatsAppService;
use App\Services\AuditService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;
    public int $backoff = 60;

    public function __construct(
        public Ticket $ticket,
        public string $channel = 'all' // 'email' | 'whatsapp' | 'all'
    ) {}

    public function handle(EmailService $email, WhatsAppService $whatsapp): void
    {
        $results = [];

        if (in_array($this->channel, ['email', 'all']) && $this->ticket->buyer_email) {
            $results['email'] = $email->sendTicketConfirmation($this->ticket);
        }

        if (in_array($this->channel, ['whatsapp', 'all']) && $this->ticket->buyer_phone) {
            $results['whatsapp'] = $whatsapp->sendTicketConfirmation($this->ticket);
        }

        AuditService::log('sent_ticket_notification', $this->ticket, [], $results);
        Log::info("SendTicketJob: {$this->ticket->ticket_code}", $results);
    }

    public function failed(\Throwable $e): void
    {
        Log::error("SendTicketJob falhou [{$this->ticket->ticket_code}]: " . $e->getMessage());
        AuditService::log('send_ticket_failed', $this->ticket, [], ['error' => $e->getMessage()]);
    }
}
```

### Criar app/Jobs/SendPendingNotificationJob.php (NOVO)
```php
<?php
namespace App\Jobs;

use App\Models\Ticket;
use App\Services\EmailService;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPendingNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(public Ticket $ticket) {}

    public function handle(EmailService $email, WhatsAppService $whatsapp): void
    {
        if ($this->ticket->buyer_email) {
            $email->sendPaymentPending($this->ticket);
        }
        if ($this->ticket->buyer_phone) {
            $whatsapp->sendPaymentPending($this->ticket);
        }
    }
}
```

### Criar app/Jobs/SendEventReminderJob.php (NOVO)
```php
<?php
namespace App\Jobs;

use App\Models\Ticket;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEventReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Ticket $ticket) {}

    public function handle(WhatsAppService $whatsapp): void
    {
        if ($this->ticket->buyer_phone && !$this->ticket->reminder_sent_at) {
            $whatsapp->sendEventReminder($this->ticket);
            $this->ticket->update(['reminder_sent_at' => now()]);
        }
    }
}
```

---

## FASE 2E — NOVOS LIVEWIRE COMPONENTS

### app/Livewire/Admin/UserList.php (NOVO)
```php
<?php
namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\AuditService;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = '';
    public string $filterStatus = '';

    protected $queryString = ['search', 'filterRole', 'filterStatus'];

    public function updatingSearch(): void { $this->resetPage(); }

    public function toggleActive(int $userId): void
    {
        $user = User::findOrFail($userId);
        if ($user->id === auth()->id()) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Não pode desactivar a sua própria conta.']);
            return;
        }
        $old = ['is_active' => $user->is_active];
        $user->update(['is_active' => !$user->is_active]);
        AuditService::log('toggled_user_status', $user, $old, ['is_active' => $user->is_active]);

        $status = $user->is_active ? 'activado' : 'desactivado';
        $this->dispatch('toast', ['type' => 'success', 'message' => "Utilizador {$status}."]);
    }

    public function changeRole(int $userId, string $role): void
    {
        $this->authorize('manage-users');
        $user = User::findOrFail($userId);
        $old  = ['role' => $user->role];
        $user->update(['role' => $role]);
        AuditService::log('changed_user_role', $user, $old, ['role' => $role]);
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Perfil actualizado.']);
    }

    public function deleteUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        if ($user->id === auth()->id()) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Não pode eliminar a sua própria conta.']);
            return;
        }
        AuditService::log('deleted_user', $user);
        $user->delete();
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Utilizador eliminado.']);
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->filterRole, fn($q) => $q->where('role', $this->filterRole))
            ->when($this->filterStatus !== '', fn($q) => $q->where('is_active', $this->filterStatus === 'active'))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.user-list', [
            'users' => $users,
            'roles' => User::ROLES,
        ]);
    }
}
```

### app/Livewire/Admin/UserForm.php (NOVO)
```php
<?php
namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UserForm extends Component
{
    use WithFileUploads;

    public ?User $user = null;
    public string $name     = '';
    public string $email    = '';
    public string $phone    = '';
    public string $role     = 'operator';
    public string $password = '';
    public bool   $isEdit   = false;
    public $avatar = null; // uploaded file

    protected function rules(): array
    {
        $emailRule = $this->isEdit
            ? "required|email|unique:users,email,{$this->user?->id}"
            : 'required|email|unique:users,email';

        return [
            'name'     => 'required|min:3|max:100',
            'email'    => $emailRule,
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:' . implode(',', array_keys(User::ROLES)),
            'password' => $this->isEdit ? 'nullable|min:8' : 'required|min:8',
            'avatar'   => 'nullable|image|max:2048',
        ];
    }

    public function mount(?User $user = null): void
    {
        if ($user?->exists) {
            $this->isEdit = true;
            $this->user   = $user;
            $this->name   = $user->name;
            $this->email  = $user->email;
            $this->phone  = $user->phone ?? '';
            $this->role   = $user->role;
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role'  => $this->role,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->avatar) {
            $data['avatar'] = $this->avatar->store('avatars', 'public');
        }

        if ($this->isEdit) {
            $old = $this->user->toArray();
            $this->user->update($data);
            AuditService::log('updated_user', $this->user, $old, $data);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Utilizador actualizado.']);
        } else {
            $data['is_active'] = true;
            $user = User::create($data);
            AuditService::log('created_user', $user, [], $data);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Utilizador criado com sucesso.']);
            $this->reset(['name', 'email', 'phone', 'password', 'avatar']);
        }
    }

    public function render()
    {
        return view('livewire.admin.user-form', ['roles' => User::ROLES]);
    }
}
```

### app/Livewire/Admin/Profile.php (NOVO)
```php
<?php
namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public string $name     = '';
    public string $email    = '';
    public string $phone    = '';
    public string $current_password = '';
    public string $new_password     = '';
    public string $new_password_confirmation = '';
    public $avatar = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name  = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    public function saveProfile(): void
    {
        $this->validate([
            'name'   => 'required|min:3',
            'email'  => 'required|email|unique:users,email,' . auth()->id(),
            'phone'  => 'nullable|string|max:20',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $data = ['name' => $this->name, 'email' => $this->email, 'phone' => $this->phone];

        if ($this->avatar) {
            if (auth()->user()->avatar) {
                Storage::disk('public')->delete(auth()->user()->avatar);
            }
            $data['avatar'] = $this->avatar->store('avatars', 'public');
        }

        auth()->user()->update($data);
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Perfil actualizado.']);
    }

    public function changePassword(): void
    {
        $this->validate([
            'current_password'             => 'required',
            'new_password'                 => 'required|min:8|confirmed',
            'new_password_confirmation'    => 'required',
        ]);

        if (!Hash::check($this->current_password, auth()->user()->password)) {
            $this->addError('current_password', 'Palavra-passe actual incorrecta.');
            return;
        }

        auth()->user()->update(['password' => Hash::make($this->new_password)]);
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Palavra-passe alterada com sucesso.']);
    }

    public function render()
    {
        return view('livewire.admin.profile');
    }
}
```

### app/Livewire/Admin/SiteSettings.php (NOVO)
```php
<?php
namespace App\Livewire\Admin;

use App\Models\SiteSetting;
use App\Services\AuditService;
use Livewire\Component;
use Livewire\WithFileUploads;

class SiteSettings extends Component
{
    use WithFileUploads;

    // Grupo: event
    public string $event_name        = '';
    public string $event_date        = '';
    public string $event_time        = '';
    public string $event_venue       = '';
    public string $event_city        = '';
    public string $event_description = '';
    public string $event_contact_1   = '';
    public string $event_contact_2   = '';

    // Grupo: social
    public string $social_facebook  = '';
    public string $social_instagram = '';
    public string $social_tiktok    = '';
    public string $social_youtube   = '';
    public string $social_whatsapp  = '';

    // Banner
    public string $banner_title    = '';
    public string $banner_subtitle = '';
    public $banner_image = null;

    public string $activeTab = 'event';

    public function mount(): void
    {
        $event  = SiteSetting::group('event');
        $social = SiteSetting::group('social');
        $banner = SiteSetting::group('banner');

        foreach ($event as $key => $value) {
            if (property_exists($this, "event_{$key}")) {
                $this->{"event_{$key}"} = $value ?? '';
            }
        }
        foreach ($social as $key => $value) {
            if (property_exists($this, "social_{$key}")) {
                $this->{"social_{$key}"} = $value ?? '';
            }
        }
        $this->banner_title    = $banner['title'] ?? '';
        $this->banner_subtitle = $banner['subtitle'] ?? '';
    }

    public function saveEvent(): void
    {
        $fields = ['name', 'date', 'time', 'venue', 'city', 'description', 'contact_1', 'contact_2'];
        foreach ($fields as $field) {
            SiteSetting::set("event_{$field}", $this->{"event_{$field}"});
        }
        AuditService::log('updated_site_settings', null, [], ['group' => 'event']);
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Informações do evento guardadas.']);
    }

    public function saveSocial(): void
    {
        $fields = ['facebook', 'instagram', 'tiktok', 'youtube', 'whatsapp'];
        foreach ($fields as $field) {
            SiteSetting::set("social_{$field}", $this->{"social_{$field}"});
        }
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Redes sociais guardadas.']);
    }

    public function saveBanner(): void
    {
        SiteSetting::set('banner_title', $this->banner_title);
        SiteSetting::set('banner_subtitle', $this->banner_subtitle);
        if ($this->banner_image) {
            $path = $this->banner_image->store('banners', 'public');
            SiteSetting::set('banner_image', $path);
        }
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Banner guardado.']);
    }

    public function render()
    {
        return view('livewire.admin.site-settings');
    }
}
```

### app/Livewire/Admin/BatchManager.php (NOVO)
```php
<?php
namespace App\Livewire\Admin;

use App\Models\TicketBatch;
use App\Models\Event;
use Livewire\Component;

class BatchManager extends Component
{
    public int    $eventId    = 0;
    public string $name       = '';
    public string $description = '';
    public string $ticket_type = 'promotional';
    public int    $price      = 0;
    public int    $quantity   = 100;
    public string $starts_at  = '';
    public string $ends_at    = '';
    public bool   $is_active  = true;
    public int    $sort_order = 0;
    public ?int   $editingId  = null;

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
            'name' => $this->name, 'description' => $this->description,
            'ticket_type' => $this->ticket_type, 'price' => $this->price,
            'quantity' => $this->quantity, 'starts_at' => $this->starts_at ?: null,
            'ends_at' => $this->ends_at ?: null, 'is_active' => $this->is_active,
            'sort_order' => $this->sort_order, 'event_id' => $this->eventId,
        ];

        if ($this->editingId) {
            TicketBatch::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Lote actualizado.']);
        } else {
            TicketBatch::create($data);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Lote criado.']);
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
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Estado do lote alterado.']);
    }

    public function delete(int $id): void
    {
        TicketBatch::findOrFail($id)->delete();
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Lote eliminado.']);
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
        ]);
    }
}
```

### app/Livewire/Admin/QuickSale.php (NOVO)
```php
<?php
namespace App\Livewire\Admin;

use App\Models\TicketBatch;
use App\Models\Event;
use App\Services\TicketService;
use App\Services\AuditService;
use App\Jobs\SendTicketJob;
use Livewire\Component;

class QuickSale extends Component
{
    public int    $batchId       = 0;
    public int    $quantity      = 1;
    public string $payment_method = 'cash';
    public string $buyer_name    = '';
    public string $buyer_phone   = '';
    public string $buyer_email   = '';
    public string $notes         = '';
    public bool   $isQuickMode   = true; // true = sem dados pessoais obrigatórios
    public array  $createdTickets = [];
    public bool   $showSuccess   = false;

    protected function rules(): array
    {
        return [
            'batchId'        => 'required|exists:ticket_batches,id',
            'quantity'       => 'required|integer|min:1|max:20',
            'payment_method' => 'required|in:mpesa,emola,cash,bank_transfer,other',
            'buyer_name'     => $this->isQuickMode ? 'nullable' : 'required|min:3',
            'buyer_phone'    => 'nullable|string',
            'buyer_email'    => 'nullable|email',
        ];
    }

    public function sale(TicketService $ticketService): void
    {
        $this->validate();
        $batch = TicketBatch::findOrFail($this->batchId);

        if (!$batch->isAvailable()) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Lote indisponível ou esgotado.']);
            return;
        }

        if ($batch->available < $this->quantity) {
            $this->dispatch('toast', ['type' => 'error', 'message' => "Apenas {$batch->available} bilhetes disponíveis."]);
            return;
        }

        $tickets = [];
        for ($i = 0; $i < $this->quantity; $i++) {
            $ticket = $ticketService->createTicket([
                'event_id'       => $batch->event_id,
                'batch_id'       => $batch->id,
                'ticket_type'    => $batch->ticket_type,
                'price'          => $batch->price,
                'payment_method' => $this->payment_method,
                'payment_ref'    => 'PRESENCIAL-' . strtoupper(substr(uniqid(), -6)),
                'buyer_name'     => $this->buyer_name ?: "Venda Rápida #{$i+1}",
                'buyer_phone'    => $this->buyer_phone ?: null,
                'buyer_email'    => $this->buyer_email ?: null,
                'ticket_mode'    => $this->isQuickMode ? 'quick_sale' : 'personalized',
                'status'         => 'confirmed', // venda presencial = confirmado imediatamente
                'notes'          => $this->notes ?: null,
            ]);
            $tickets[] = $ticket;

            // Enviar notificações se tem contacto
            if ($ticket->buyer_phone || $ticket->buyer_email) {
                SendTicketJob::dispatch($ticket, 'all')->delay(now()->addSeconds(5));
            }
        }

        $batch->increment('sold', $this->quantity);
        AuditService::log('quick_sale', null, [], [
            'batch' => $batch->name,
            'quantity' => $this->quantity,
            'payment' => $this->payment_method,
            'total' => $batch->price * $this->quantity,
        ]);

        $this->createdTickets = $tickets;
        $this->showSuccess    = true;
        $this->reset(['quantity', 'buyer_name', 'buyer_phone', 'buyer_email', 'notes']);
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "{$this->quantity} bilhete(s) gerado(s) e confirmado(s).",
        ]);
    }

    public function render()
    {
        $batches = TicketBatch::where('is_active', true)
            ->where('event_id', Event::where('is_active', true)->value('id'))
            ->orderBy('sort_order')
            ->get();

        return view('livewire.admin.quick-sale', compact('batches'));
    }
}
```

### app/Livewire/Admin/AuditLogs.php (NOVO)
```php
<?php
namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogs extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterAction = '';

    public function render()
    {
        $logs = AuditLog::with('user')
            ->when($this->search, fn($q) => $q->whereHas('user', fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
            ))
            ->when($this->filterAction, fn($q) => $q->where('action', $this->filterAction))
            ->latest()
            ->paginate(25);

        $actions = AuditLog::distinct()->pluck('action')->sort();

        return view('livewire.admin.audit-logs', compact('logs', 'actions'));
    }
}
```

---

## FASE 2F — ROTAS NOVAS (adicionar a routes/web.php)

NÃO substituir as rotas existentes. ADICIONAR ao grupo admin:

```php
use App\Livewire\Admin\UserList;
use App\Livewire\Admin\UserForm;
use App\Livewire\Admin\Profile;
use App\Livewire\Admin\SiteSettings;
use App\Livewire\Admin\BatchManager;
use App\Livewire\Admin\QuickSale;
use App\Livewire\Admin\AuditLogs;

Route::middleware(['auth', 'check.role:admin,organizer,super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Rotas EXISTENTES — não tocar
        Route::get('/', AdminDashboard::class)->name('dashboard');
        Route::get('/tickets', TicketList::class)->name('tickets');
        Route::get('/tickets/export', [AdminController::class, 'exportCsv'])->name('tickets.export');
        Route::get('/manual', ManualTicketForm::class)->name('manual');

        // Rotas NOVAS — adicionar
        Route::get('/users', UserList::class)->name('users.index');
        Route::get('/users/create', UserForm::class)->name('users.create');
        Route::get('/users/{user}/edit', UserForm::class)->name('users.edit');
        Route::get('/profile', Profile::class)->name('profile');
        Route::get('/settings', SiteSettings::class)->name('settings');
        Route::get('/batches', BatchManager::class)->name('batches');
        Route::get('/quick-sale', QuickSale::class)->name('quick-sale');
        Route::get('/audit', AuditLogs::class)->name('audit');
    });
```

Actualizar middleware CheckRole para aceitar múltiplos roles:
```php
// app/Http/Middleware/CheckRole.php
public function handle($request, Closure $next, string ...$roles): mixed
{
    if (!auth()->check()) return redirect()->route('login');

    $user = auth()->user();
    if (!$user->is_active) {
        auth()->logout();
        return redirect()->route('login')->withErrors(['email' => 'Conta desactivada.']);
    }

    if (!empty($roles) && !in_array($user->role, $roles)) {
        abort(403, 'Sem permissão para aceder a esta área.');
    }

    return $next($request);
}
```

---

## FASE 2G — LAYOUT ADMIN ACTUALIZADO

### resources/views/layouts/admin.blade.php

Reconstruir com:

**Sidebar** (desktop: fixa 220px, mobile: drawer overlay):
```
Logo Alpha Produções + "Bilheteira Digital"
────────────────────────────────────
Dashboard          (layout-dashboard)
Bilhetes           (ticket)
Lotes              (layers)
Nova Venda         (shopping-cart)
────────────────────────────────────
Utilizadores       (users)  [admin only]
Conteúdo do Site   (globe)  [admin only]
Auditoria          (shield) [admin only]
────────────────────────────────────
Scanner/Validador  (scan)
────────────────────────────────────
O meu Perfil       (user-circle)
Sair               (log-out)
```

**Topbar** (mobile: hamburger + logo + avatar):
```
[≡] TÍTULO DA PÁGINA    [⏱ 37 dias] [avatar + nome ▾]
```
Avatar: foto do utilizador ou initials via ui-avatars.com
Dropdown: Perfil / Configurações / Sair

**Toast container** (Livewire events via Alpine):
```html
<div x-data="{ toasts: [] }"
     @toast.window="
       toasts.push({...$event.detail, id: Date.now()});
       setTimeout(() => toasts.shift(), 3500)
     "
     class="fixed bottom-6 right-6 z-50 flex flex-col gap-3">
  <template x-for="t in toasts" :key="t.id">
    <div class="toast-item" :class="t.type">
      <span x-text="t.message"></span>
    </div>
  </template>
</div>
```

---

## FASE 2H — RESPONSIVIDADE

### Tabelas → Cards no mobile

Em todas as views de tabela, usar este padrão:

```html
{{-- Desktop: tabela normal --}}
<div class="hidden md:block">
  <table>...</table>
</div>

{{-- Mobile: cards --}}
<div class="md:hidden space-y-3">
  @foreach($tickets as $ticket)
  <div class="mobile-ticket-card">
    <div class="flex justify-between items-start">
      <div>
        <p class="ticket-code">{{ $ticket->ticket_code }}</p>
        <p class="ticket-name">{{ $ticket->buyer_name }}</p>
      </div>
      <span class="status-badge {{ $ticket->status }}">{{ $ticket->status }}</span>
    </div>
    <div class="flex justify-between items-center mt-2">
      <span class="ticket-type">{{ $ticket->ticket_type }}</span>
      <span class="ticket-date">{{ $ticket->created_at->format('d/m H:i') }}</span>
    </div>
    <div class="flex gap-2 mt-3">
      @if($ticket->status === 'pending')
        <button wire:click="confirmTicket({{ $ticket->id }})" class="btn-confirm-sm">
          Confirmar
        </button>
      @endif
      <button wire:click="viewTicket({{ $ticket->id }})" class="btn-view-sm">
        Ver
      </button>
    </div>
  </div>
  @endforeach
</div>
```

Dashboard cards: usar grid responsivo:
```html
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
  <!-- stat cards -->
</div>
```

---

## FASE 2I — SEEDER PARA SITE SETTINGS

Criar database/seeders/SiteSettingsSeeder.php:

```php
<?php
namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Evento
            ['key' => 'event_name',        'value' => 'Concerto Renúncia',             'group' => 'event',  'type' => 'text'],
            ['key' => 'event_date',        'value' => '2026-07-11',                    'group' => 'event',  'type' => 'text'],
            ['key' => 'event_time',        'value' => '16:00',                         'group' => 'event',  'type' => 'text'],
            ['key' => 'event_venue',       'value' => 'Pavilhão do Benfica',           'group' => 'event',  'type' => 'text'],
            ['key' => 'event_city',        'value' => 'Quelimane, Mozambique',         'group' => 'event',  'type' => 'text'],
            ['key' => 'event_contact_1',   'value' => '87 541 1644',                   'group' => 'event',  'type' => 'text'],
            ['key' => 'event_contact_2',   'value' => '84 887 1940',                   'group' => 'event',  'type' => 'text'],
            // Social
            ['key' => 'social_facebook',   'value' => 'https://facebook.com/alphaproducoes',  'group' => 'social', 'type' => 'text'],
            ['key' => 'social_instagram',  'value' => 'https://instagram.com/alphaproducoes', 'group' => 'social', 'type' => 'text'],
            ['key' => 'social_tiktok',     'value' => 'https://tiktok.com/@alphaproducoes',   'group' => 'social', 'type' => 'text'],
            ['key' => 'social_whatsapp',   'value' => 'https://wa.me/258875411644',           'group' => 'social', 'type' => 'text'],
            // Banner
            ['key' => 'banner_title',      'value' => 'RENÚNCIA',                      'group' => 'banner', 'type' => 'text'],
            ['key' => 'banner_subtitle',   'value' => 'Abel Last & Nair Nany em Concerto', 'group' => 'banner', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
```

Correr: `php artisan db:seed --class=SiteSettingsSeeder --force`

---

## FASE 2J — SEEDER PARA LOTES

Criar database/seeders/TicketBatchSeeder.php:

```php
<?php
namespace Database\Seeders;

use App\Models\TicketBatch;
use App\Models\Event;
use Illuminate\Database\Seeder;

class TicketBatchSeeder extends Seeder
{
    public function run(): void
    {
        $event = Event::where('is_active', true)->first();
        if (!$event) return;

        $batches = [
            ['name' => 'Bilhete Promocional', 'ticket_type' => 'promotional', 'price' => 500,  'quantity' => 200, 'sort_order' => 1],
            ['name' => 'Segundo Lote',        'ticket_type' => 'second_lot',  'price' => 750,  'quantity' => 300, 'sort_order' => 2],
            ['name' => 'No Portão',           'ticket_type' => 'gate',        'price' => 1000, 'quantity' => 9999,'sort_order' => 3],
            ['name' => 'VIP',                 'ticket_type' => 'vip',         'price' => 2000, 'quantity' => 50,  'sort_order' => 4],
            ['name' => 'Cortesia',            'ticket_type' => 'free',        'price' => 0,    'quantity' => 50,  'sort_order' => 5],
        ];

        foreach ($batches as $batch) {
            TicketBatch::updateOrCreate(
                ['name' => $batch['name'], 'event_id' => $event->id],
                array_merge($batch, ['event_id' => $event->id, 'is_active' => true])
            );
        }
    }
}
```

---

## FASE 2K — COMANDO DE LEMBRETES

Criar app/Console/Commands/SendEventReminders.php:

```php
<?php
namespace App\Console\Commands;

use App\Models\Ticket;
use App\Jobs\SendEventReminderJob;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    protected $signature   = 'tickets:send-reminders {--dry-run : Mostrar sem enviar}';
    protected $description = 'Enviar lembretes 24h antes do evento';

    public function handle(): int
    {
        $tickets = Ticket::where('status', 'confirmed')
                         ->whereNull('reminder_sent_at')
                         ->whereNotNull('buyer_phone')
                         ->get();

        $this->info("Bilhetes elegíveis: {$tickets->count()}");

        if ($this->option('dry-run')) {
            $tickets->each(fn($t) => $this->line("  · {$t->ticket_code} — {$t->buyer_name} — {$t->buyer_phone}"));
            return 0;
        }

        $tickets->each(fn($t) => SendEventReminderJob::dispatch($t));
        $this->info("✓ {$tickets->count()} jobs de lembrete criados.");
        return 0;
    }
}
```

Registar em routes/console.php:
```php
use Illuminate\Support\Facades\Schedule;
use Carbon\Carbon;

Schedule::command('tickets:send-reminders')
    ->dailyAt('09:00')
    ->when(fn() => Carbon::today()->isSameDay(Carbon::parse('2026-07-10')));
```

---

## ORDEM DE IMPLEMENTAÇÃO

Execute nesta sequência exacta para não quebrar produção:

```
1.  Criar todas as migrations (Fase 2A)
2.  php artisan migrate --force
3.  Actualizar Models (Fase 2B) — apenas addições
4.  Criar Services novos (Fase 2C)
5.  Actualizar SendTicketJob + criar novos Jobs (Fase 2D)
6.  Criar Livewire components (Fase 2E)
7.  Adicionar rotas novas em web.php (Fase 2F)
8.  Actualizar layout admin (Fase 2G)
9.  Responsividade nas views existentes (Fase 2H)
10. php artisan db:seed --class=SiteSettingsSeeder --force
11. php artisan db:seed --class=TicketBatchSeeder --force
12. Criar comando de lembretes (Fase 2K)
13. npm run build
14. php artisan optimize:clear && php artisan config:cache && php artisan route:cache
15. Deploy via Git: git add . && git commit -m "feat: fase 2 - users, batches, notifications, settings" && git push
16. No servidor: setup.php → Git Pull + Deploy
```

---

## O QUE NÃO IMPLEMENTAR AGORA (adiar pós-evento)

❌ Compra de múltiplos bilhetes com dados por participante
   → O formulário actual funciona. Adicionar após Julho 2026.

❌ Scanner com BarcodeDetector + html5-qrcode simultâneos
   → Já está funcional. Não tocar.

❌ Relatórios PDF exportáveis
   → CSV já existe. PDF de relatórios é pós-evento.

❌ FAQ e Galeria dinâmicas no site público
   → Não há tempo antes de 11 de Julho. Adiar.

❌ Gateway de pagamento automático M-Pesa
   → A integração API M-Pesa requer aprovação da Vodacom MZ.
   → Manter fluxo manual actual + confirmação do admin.
```