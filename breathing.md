# BRIEFING COMPLETO — FASE 2
# Sistema Bilhetagem Digital · Concerto Renúncia 2026
# Para: Claude Code Agent
# ════════════════════════════════════════════════════════════

## LEIA ESTE DOCUMENTO COMPLETO ANTES DE ESCREVER UMA LINHA DE CÓDIGO

---

## 1. QUEM SOU E ONDE ESTAMOS

Sou o developer do sistema de bilhetagem digital para o
**Concerto Renúncia** (Abel Last & Nair Nany), 11 Julho 2026,
Pavilhão do Benfica, Quelimane, Moçambique.

O sistema está **em produção** em:
https://ineds.org/alpha/bilhetes

Já foram vendidos bilhetes reais. Há utilizadores reais.
Há dados reais na base de dados de produção.

---

## 2. AMBIENTE TÉCNICO

```
PHP:        8.3.31
Laravel:    13.12.0  (atenção: não é Laravel 11 — é 13)
Livewire:   3.x
Tailwind:   CSS via Vite
Alpine.js:  3.x
MySQL:      fili3528_larav75 (HostGator cPanel)
Queue:      database driver (sem Redis, sem Supervisor)
Hosting:    HostGator shared cPanel
Deploy:     Git → github.com/filipeive/bilheteira_digital
URL base:   https://ineds.org/alpha/bilhetes
```

---

## 3. O QUE JÁ EXISTE E ESTÁ FUNCIONAL

### Base de dados (tabelas existentes com dados reais):
```
users          — utilizadores com roles: admin, operator, organizer
events         — 1 evento activo (Concerto Renúncia)
tickets        — BILHETES VENDIDOS (dados reais, não tocar!)
sessions       — sessões activas
jobs           — queue de jobs
failed_jobs    — jobs falhados
cache          — cache
```

### Estrutura de ficheiros existente:
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── PublicTicketController.php   ✅ funcional
│   │   ├── AdminController.php          ✅ funcional
│   │   └── Api/ValidationController.php ✅ funcional
│   └── Middleware/
│       └── CheckRole.php                ✅ funcional
├── Livewire/
│   ├── TicketForm.php                   ✅ funcional
│   ├── AdminDashboard.php               ✅ funcional
│   ├── TicketList.php                   ✅ funcional
│   └── ManualTicketForm.php             ✅ funcional
├── Jobs/
│   ├── SendTicketJob.php                ✅ existe (melhorar)
│   └── TicketMail.php                   ✅ existe
├── Models/
│   ├── Event.php                        ✅ funcional
│   ├── Ticket.php                       ✅ funcional (tem dados reais)
│   └── User.php                         ✅ funcional (tem users reais)
└── Services/
    ├── TicketService.php                ✅ funcional
    ├── QrCodeService.php                ✅ funcional
    └── MpesaService.php                 ✅ estrutura base

resources/views/
├── layouts/
│   ├── public.blade.php                 ✅ funcional
│   └── admin.blade.php                  ✅ funcional (melhorar)
├── public/sale.blade.php                ✅ funcional
├── admin/
│   ├── dashboard.blade.php              ✅ funcional
│   └── tickets.blade.php               ✅ funcional
├── validator/scanner.blade.php          ✅ funcional
├── pdf/ticket.blade.php                 ✅ funcional
└── emails/ticket.blade.php             ✅ existe

routes/
├── web.php                              ✅ 41 rotas registadas
└── api.php                              ✅ funcional
```

### Design System (PRESERVAR EXACTAMENTE):
```
Background:  #0D0B07
Surface:     #1E1810
Surface2:    #111009
Gold:        #D4A017
Gold Light:  #F5C540
Verde:       #3DBA7C
Vermelho:    #E05454
Laranja:     #E08A3A
Texto:       #F0E8D5
Texto dim:   rgba(240,232,213,0.45)

Fontes:
  Display:   Bebas Neue (títulos grandes)
  Corpo:     Montserrat (400, 500, 700)
  Mono:      JetBrains Mono (IDs, labels, código)

Ícones:      Lucide Icons (data-lucide="...")
             lucide.createIcons() após render
```

---

## 4. REGRAS ABSOLUTAS — NUNCA VIOLAR

```
❌ NUNCA correr php artisan migrate:fresh
❌ NUNCA correr php artisan migrate:fresh --seed
❌ NUNCA chamar Ticket::truncate()
❌ NUNCA chamar DB::table('tickets')->truncate()
❌ NUNCA correr TicketSeeder (cria tickets de teste)
❌ NUNCA correr UserSeeder (pode duplicar users)
❌ NUNCA renomear tabelas existentes
❌ NUNCA remover colunas existentes
❌ NUNCA mudar nomes de rotas existentes
❌ NUNCA alterar migrations já corridas
❌ NUNCA usar localStorage nos assets (não funciona no Claude.ai)
```

```
✅ SEMPRE criar migrations aditivas (add_*, create_* novas tabelas)
✅ SEMPRE usar updateOrCreate nos seeders novos
✅ SEMPRE correr seeders individualmente: --class=NomeDoSeeder
✅ SEMPRE testar em ambiente local antes de sugerir deploy
✅ SEMPRE preservar o design system definido acima
✅ SEMPRE usar ASSET_URL=https://ineds.org/alpha/bilhetes
✅ SEMPRE queue driver=database (sem Redis)
✅ SEMPRE dispatch jobs com ->delay() quando apropriado
```

---

## 5. ESTADO DA BASE DE DADOS — VERIFICAÇÃO PRÉVIA

**ANTES de propor qualquer migration, verifica o estado actual.**

Pede-me para correr este comando e mostrar o output:

```bash
php artisan migrate:status
```

E também:
```bash
php artisan tinker --execute="
echo 'Users: ' . App\Models\User::count() . PHP_EOL;
echo 'Events: ' . App\Models\Event::count() . PHP_EOL;
echo 'Tickets: ' . App\Models\Ticket::count() . PHP_EOL;
echo 'Tickets confirmados: ' . App\Models\Ticket::where(\"status\",\"confirmed\")->count() . PHP_EOL;
echo 'Tickets pending: ' . App\Models\Ticket::where(\"status\",\"pending\")->count() . PHP_EOL;
echo 'Tickets used: ' . App\Models\Ticket::where(\"status\",\"used\")->count() . PHP_EOL;
"
```

Aguarda o meu output antes de prosseguir.

---

## 6. DATABASESEEDER — ESTADO ACTUAL

O `database/seeders/DatabaseSeeder.php` deve ser verificado.
Pede-me para mostrar o conteúdo actual.

Após ver, vamos garantir que fica assim para proteger dados:

```php
<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ⚠️ PRODUÇÃO — bilhetes reais existem na base de dados
        // Os seeders abaixo estão desactivados para proteger dados:

        // $this->call(UserSeeder::class);    // ← desactivado
        // $this->call(EventSeeder::class);   // ← desactivado
        // $this->call(TicketSeeder::class);  // ← NUNCA — apaga bilhetes reais

        // Apenas seeders seguros (usam updateOrCreate):
        // $this->call(SiteSettingsSeeder::class);  // activar na Fase 2
        // $this->call(TicketBatchSeeder::class);   // activar na Fase 2
    }
}
```

---

## 7. O QUE VAMOS IMPLEMENTAR NA FASE 2

Após verificares o estado actual, implementa as seguintes
melhorias por ordem de prioridade e segurança:

### PRIORIDADE 1 — Migrations aditivas (sem risco)
Novas tabelas e colunas. Não toca no que existe.

```
create_audit_logs_table
create_site_settings_table
create_ticket_batches_table
add_notification_columns_to_tickets_table
  └── email_sent_at, whatsapp_sent_at, reminder_sent_at,
      ticket_mode, batch_id, scanned_device
add_profile_columns_to_users_table
  └── phone, avatar, is_active
      role: mudar de enum para string (compatível com roles novos)
```

**Atenção na coluna `role`:**
Se a coluna `role` já existe como enum, a migration de mudança
para string deve ser:
```php
// VERIFICAR PRIMEIRO se é enum ou string:
// SHOW COLUMNS FROM users LIKE 'role';

// Se for enum, mudar para string:
$table->string('role')->default('operator')->change();
```

### PRIORIDADE 2 — Novos Models (sem risco)
Ficheiros novos, não sobrepõem nada existente:
```
app/Models/AuditLog.php
app/Models/SiteSetting.php
app/Models/TicketBatch.php
```

Actualizações aos models existentes:
```
app/Models/User.php    — adicionar métodos e constante ROLES
app/Models/Ticket.php  — adicionar relação batch() e helpers
```

### PRIORIDADE 3 — Novos Services
```
app/Services/EmailService.php       (NOVO)
app/Services/WhatsAppService.php    (NOVO)
app/Services/AuditService.php       (NOVO)
```

### PRIORIDADE 4 — Actualizar Jobs existentes
```
app/Jobs/SendTicketJob.php           (ACTUALIZAR — não substituir estrutura)
app/Jobs/SendPendingNotificationJob.php  (NOVO)
app/Jobs/SendEventReminderJob.php        (NOVO)
```

### PRIORIDADE 5 — Novos Mails e Views de Email
```
app/Mail/TicketConfirmationMail.php   (NOVO)
app/Mail/PaymentPendingMail.php       (NOVO)
resources/views/emails/ticket-confirmation.blade.php  (NOVO)
resources/views/emails/payment-pending.blade.php      (NOVO)
```

### PRIORIDADE 6 — Novos Livewire Components
Todos em `app/Livewire/Admin/` (subpasta nova, sem conflito):
```
Admin/UserList.php
Admin/UserForm.php
Admin/Profile.php
Admin/SiteSettings.php
Admin/BatchManager.php
Admin/QuickSale.php
Admin/AuditLogs.php
```

### PRIORIDADE 7 — Novas Views (não sobrepõem existentes)
```
resources/views/livewire/admin/user-list.blade.php
resources/views/livewire/admin/user-form.blade.php
resources/views/livewire/admin/profile.blade.php
resources/views/livewire/admin/site-settings.blade.php
resources/views/livewire/admin/batch-manager.blade.php
resources/views/livewire/admin/quick-sale.blade.php
resources/views/livewire/admin/audit-logs.blade.php
```

### PRIORIDADE 8 — Actualizar rotas (apenas adicionar)
Em `routes/web.php`, ADICIONAR ao grupo admin existente.
Não remover nem alterar rotas existentes.

### PRIORIDADE 9 — Melhorar layout admin
`resources/views/layouts/admin.blade.php` — melhorar sidebar,
topbar com avatar, toast container. Preservar o que funciona.

### PRIORIDADE 10 — Responsividade
Adicionar variante mobile (cards) às views de tabela existentes.

### PRIORIDADE 11 — Seeders seguros (updateOrCreate)
```
database/seeders/SiteSettingsSeeder.php  (NOVO)
database/seeders/TicketBatchSeeder.php   (NOVO)
```

### PRIORIDADE 12 — Comando de lembretes
```
app/Console/Commands/SendEventReminders.php  (NOVO)
```

---

## 8. CONFIGURAÇÕES .ENV NECESSÁRIAS

Após as implementações, precisarei de adicionar ao `.env`:

```env
# Email (Gmail App Password)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seuemail@gmail.com
MAIL_PASSWORD=xxxx-xxxx-xxxx-xxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=bilhetes@alphaproducoes.mz
MAIL_FROM_NAME="Bilheteira Renúncia"

# WhatsApp Cloud API (Meta)
WHATSAPP_TOKEN=EAAxxxxxxxxxx
WHATSAPP_PHONE_ID=1234567890
WHATSAPP_BUSINESS_ID=0987654321

# Africa's Talking (SMS fallback)
AT_USERNAME=alphaproducoes
AT_API_KEY=xxxxxxxxxxxxxxxxxxxx
AT_SENDER_ID=RENUNCIA
```

Não geres estas credenciais — deixa placeholders.
Vou preencher manualmente.

---

## 9. SEQUÊNCIA EXACTA DE DEPLOY (após implementação)

Quando tudo estiver pronto, o deploy segue esta ordem:

```bash
# 1. Local — verificar que não há erros
php artisan migrate --pretend    # simular migrations sem correr
php artisan route:list           # verificar rotas novas

# 2. Local — commit e push
git add .
git commit -m "feat: fase 2 - users, batches, notifications, settings, audit"
git push origin main

# 3. Servidor — via setup.php
# Aceder a: https://ineds.org/public/setup.php?key=renuncia2026
# Clicar: Git Pull + Deploy
# Depois: Migrate (apenas --force, não fresh)
# Depois: db:seed --class=SiteSettingsSeeder
# Depois: db:seed --class=TicketBatchSeeder
```

---

## 10. VERIFICAÇÕES ANTES DE FECHAR CADA PRIORIDADE

Para cada prioridade implementada, confirma:

```
□ Não modifica tabelas/colunas existentes com dados
□ Não altera rotas existentes (apenas adiciona)
□ Não sobrepõe views existentes que funcionam
□ Usa o design system correcto (cores, fontes, ícones Lucide)
□ Livewire components em App\Livewire\Admin\ (subpasta)
□ Views em resources/views/livewire/admin/ (subpasta)
□ Jobs usam queue connection database
□ Seeders usam updateOrCreate
□ Asset URLs com ASSET_URL base
□ Nenhum emoji como ícone de interface (usar Lucide)
```

---

## 11. PERGUNTAS QUE DEVES FAZER ANTES DE COMEÇAR

1. Mostra-me o output de `php artisan migrate:status`
2. Mostra-me o conteúdo de `database/seeders/DatabaseSeeder.php`
3. Mostra-me o conteúdo de `app/Models/User.php` (verificar tipo do campo role)
4. Mostra-me o conteúdo de `app/Jobs/SendTicketJob.php` (para não duplicar lógica)
5. Confirma: a coluna `role` na tabela `users` é enum ou string?

Aguarda as minhas respostas antes de propor qualquer migration.

---

## 12. CONTEXTO DE NEGÓCIO

- O evento é a 11 de Julho de 2026
- Faltam ~5 semanas
- Já há bilhetes vendidos — os dados são reais e preciosos
- A prioridade máxima é: não quebrar o que funciona
- As notificações (email + WhatsApp) são a funcionalidade mais urgente
- A gestão de utilizadores é segunda prioridade
- O resto pode ser implementado gradualmente

---

## CONFIRMAÇÃO DE LEITURA

Antes de começar, responde com:

"Briefing lido. Sistema em produção com dados reais.
Vou verificar o estado actual antes de qualquer implementação.
Aguardo os outputs de migrate:status e DatabaseSeeder."

Só depois disto podes começar.
```