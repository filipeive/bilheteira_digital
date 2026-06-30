# 🏗️ Arquitetura do Sistema

Este documento descreve a estrutura técnica, fluxo de dados e os principais componentes da plataforma **Concerto Renúncia / Bilheteira Digital**.

---

## 🛠️ Stack Tecnológica
- **Backend:** Laravel 10/11
- **Componentes Dinâmicos:** Laravel Livewire v3
- **Frontend & Estilo:** HTML5, CSS3 Customizado (Gold & Dark Theme)
- **Base de Dados:** MySQL
- **Dependências de Ícones:** Lucide Icons (carregado via CDN ou localmente)

---

## 📂 Estrutura de Diretórios Importantes
```
bilhetes-app/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AdminController.php         # Controla páginas e uploads do admin
│   │       └── PublicTicketController.php   # Controla páginas públicas e fluxo de compra
│   ├── Livewire/
│   │   ├── Admin/
│   │   │   ├── NotificationsManager.php    # Gestão de Notificações
│   │   │   └── QuickSale.php               # Venda Rápida presencial
│   │   └── TicketList.php                  # Listagem e edição em massa de bilhetes
│   ├── Models/
│   │   ├── Event.php                       # Modelo do Evento (Ativo/Inativo)
│   │   ├── SiteSetting.php                 # Modelo de Chave-Valor para o site
│   │   ├── Ticket.php                      # Modelo do Bilhete (UUID, status, etc)
│   │   └── TicketBatch.php                 # Modelo de Lotes/Preços
│   └── Services/
│       ├── AuditService.php                # Logs de auditoria administrativa
│       └── TicketService.php               # Regras de negócio de validação/confirmação
├── docs/                                   # Documentação do projeto
├── resources/
│   └── views/
│       ├── admin/
│       │   └── site-content.blade.php      # Editor de conteúdo do site
│       ├── layouts/
│       │   ├── admin.blade.php             # Layout administrativo com sidebar
│       │   └── public.blade.php            # Layout público
│       ├── livewire/
│       │   ├── admin/
│       │   │   └── notifications-manager.blade.php
│       │   └── ticket-list.blade.php       # Tabela/grelha com bulk actions
│       └── public/
│           ├── about.blade.php             # Página "Sobre o Evento"
│           └── sale.blade.php              # Página inicial de vendas
└── routes/
    └── web.php                             # Rotas web do sistema
```

---

## 🗄️ Modelagem de Dados Principal

### 1. `SiteSetting`
Responsável por armazenar de forma dinâmica os textos do site, imagens e as configurações sem a necessidade de criar campos extras nas tabelas.
- Chaves Notáveis:
  - `hero_image`: Caminho da imagem de destaque.
  - `about_description`: Texto da secção "Sobre o Evento".
  - `other_artists`: String JSON com lista de artistas convidados (`[{"name": "...", "bio": "...", "photo": "..."}]`).
  - `gallery_images`: String JSON contendo array de caminhos de fotos da galeria.

### 2. `TicketBatch` (Lotes)
Controla a quantidade de bilhetes disponíveis para cada lote, preço e datas de vigência.
- Atributos principais: `name`, `price`, `quantity`, `sold` (contador incremental).

### 3. `Ticket`
Contém as informações de compra de cada bilhete, código QR gerado e dados do comprador.
- UUID como chave primária.
- Relaciona-se com `TicketBatch` (`batch_id`) e `Event` (`event_id`).
- Status possíveis: `pending`, `confirmed`, `used`, `cancelled`.

---

## 🔗 Fluxo de Ações em Massa (Bulk Actions)
Quando múltiplos bilhetes são selecionados no painel de administração (`TicketList.php`), as seguintes regras são aplicadas no processamento em lote:
1. **Alteração de Lote (Batch):** 
   - O sistema decrementa a coluna `sold` no lote antigo do bilhete.
   - O sistema incrementa a coluna `sold` no lote de destino.
   - Atualiza `batch_id`, `ticket_type` e `price` do bilhete para corresponderem ao novo lote.
2. **Alteração de Estado (Status):**
   - Atualiza o campo `status`.
   - Se alterado de/para `used`, preenche/limpa as informações de validação (`used_at`, `scanned_by`).
3. **Auditoria:** Todas as ações são logged em `AuditService` de forma individualizada com os valores antigos e novos.

---

## 🚀 Notas de Deploy (Caminhos e Symlink na HostGator)
- **Symlink Público:** O Laravel utiliza a pasta `storage/app/public` mapeada para `public/storage`.
- **Caminho das Imagens:** Para evitar erros de renderização devido à barra inicial no path gravado na base de dados (ex: `/storage/...`), utilize sempre `ltrim($path, '/')` antes de passar a variável para o helper `asset()`. Exemplo:
  ```html
  <img src="{{ asset(ltrim($settings['hero_image'], '/')) }}">
  ```
- **Ambiente HostGator:** Se o diretório público estiver mapeado para `public_html` e o projeto Laravel estiver fora da raiz, garanta que o symlink aponta corretamente para `public_html/storage` em vez de `public/storage`.
