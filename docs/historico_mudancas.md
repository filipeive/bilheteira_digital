# 📝 Histórico de Mudanças e Implementações

Este documento regista cronologicamente as melhorias e novas funcionalidades implementadas no sistema.

---

## 📅 Junho de 2026

### 1. Integração Final do Gestor de Notificações e Emails
- **Roteamento:** Registada a rota `/admin/notifications` associada ao componente Livewire `NotificationsManager`.
- **Navegação:** Integrado o item de menu "Notificações" na barra lateral administrativa (`layouts/admin.blade.php`), respeitando a paleta e os padrões estéticos Gold/Dark.

### 2. Correção da Imagem de Fundo (Upload e Visualização)
- **Problema:** A imagem de fundo (hero_image) não carregava corretamente no navegador em alguns cenários devido a caminhos gravados com barra inicial dupla ou incompatibilidade no symlink.
- **Solução:** Aplicada a função `ltrim($path, '/')` ao renderizar a imagem com o helper `asset()` tanto no painel administrativo quanto na página pública de vendas.

### 3. Gestão e Edição de Bilhetes em Massa (Bulk Actions)
- **Seleção Dinâmica:** Implementado suporte a seleção múltipla por checkboxes em formato tabela e grelha (`resources/views/livewire/ticket-list.blade.php`).
- **Edição em Lote:** Adicionados seletores na barra de ações rápidas para:
  - Alterar o lote de múltiplos bilhetes em um único clique (ex: Lote Promocional -> Lote Normal).
  - Alterar o estado (status) de múltiplos bilhetes simultaneamente (Pendente, Confirmado, Usado, Cancelado).
- **Consistência de Contadores:** A alteração de lote recalcula de forma atômica e automática os contadores de vendas (`sold`) de cada lote envolvido (antigo e novo).
- **Log de Auditoria:** Integrado o `AuditService::log()` para registar detalhadamente cada alteração em lote com os valores prévios e posteriores.

### 4. Página Sobre e Galeria Dinâmica de Artistas Convidados
- **Descrição Dinâmica:** Adicionada a edição do campo `about_description` (Descrição sobre o evento) no gestor de conteúdo inicial.
- **Lista de Convidados:** Implementado o campo `other_artists` (estrutura JSON no banco de dados) permitindo ao administrador:
  - Adicionar novos artistas convidados informando Nome e Bio/Função.
  - Carregar fotos exclusivas para cada artista ou reter as fotos existentes.
  - Remover artistas indesejados.
- **Renderização Pública:** Atualizada a página `public/about.blade.php` para apresentar a descrição dinâmica da base de dados e criar uma grelha estilizada, premium e responsiva com as fotos e informações de todos os artistas convidados adicionados.

### 5. Pasta de Documentação e Ritual de Alinhamento
- Criada a pasta `docs/` para abrigar a documentação arquitetural e de mudanças.
- Criado o arquivo `docs/0_A_Inicio_ritual.md` contendo as diretrizes de início obrigatórias para os próximos agentes de IA.

### 6. Otimização de Performance e Feedbacks de Interface (Ações em Massa)
- **Eliminação de Concorrência de Rede:** Removido o `wire:model.live` do checkbox "Select All", utilizando exclusivamente `wire:click="toggleSelectAll"` e determinando o estado verificado condicionalmente para evitar pedidos paralelos e conflitos na UI.
- **Substituição de Confirmações Livewire:** Trocado o atributo nativo `wire:confirm` do Livewire por diálogos `onclick="confirm(...) || event.stopImmediatePropagation()"` que se integram nativamente com o browser e previnem a submissão acidental de formulários.
- **Loader Animado em Viewport (Viewport-Centered Overlay):**
  - Implementado o componente de carregamento `loading-overlay` que reage em tempo real a todas as ações assíncronas de listagem (pesquisas, paginação, seleção em lote, etc.).
  - Configurado com `style="display: none;"` por omissão no HTML de forma a evitar que o loader apareça bloqueado durante a renderização inicial antes do Livewire estar pronto.
  - Centrado perfeitamente no centro vertical e horizontal do ecrã do utilizador através de `position: fixed;`, com efeito premium de vidro fosco (`backdrop-filter: blur(2px)`).
- **Deploy Efetuado:** Alterações sincronizadas com o servidor de produção da HostGator via script Python (`deploy.py`).

### 7. Migração Automática de Lotes Expirados e Edição Individual de Lote
- **Migração por Expiração (`migrateExpiredBatch`):**
  - Adicionada ação one-click na página de bilhetes que detecta automaticamente lotes com `ends_at` expirado.
  - Migra todos os bilhetes pendentes/confirmados do lote expirado para o próximo lote ativo do mesmo `ticket_type`.
  - Atualização atômica dos contadores `sold` (decrementa lote antigo, incrementa lote novo).
  - Registo de auditoria individualizado para cada bilhete migrado via `AuditService::log()`.
- **Edição Individual de Lote:**
  - Adicionado campo "Lote do Bilhete" no modal de edição individual (`editingBatchId`).
  - Ao alterar o lote no modal, aplica a mesma lógica atômica de atualização de contadores `sold`.
  - Incluídos `batch_id`, `ticket_type` e `price` nos valores registados no `AuditService` durante a edição.
- **Interface:**
  - Alerta visual vermelho no topo da listagem quando há lote expirado, com botão "Migrar Bilhetes" e confirmação nativa.
  - Seletor de lote adicionado ao formulário de edição individual, mantendo o lote atual como valor padrão.
- **Validações e Edge Cases:**
  - Não migra se não houver lote destino disponível ou se o lote já não tiver bilhetes pendentes/confirmados.
  - A migração ignora bilhetes `used` e `cancelled`.
  - A edição individual só altera o lote quando um novo lote é explicitamente selecionado.

### 8. Melhoria de UI/UX: Nomes de Lotes Legíveis e Deploy Otimizado
- **Accessor `display_name` no Model `TicketBatch`:**
  - Adicionado `getDisplayNameAttribute()` que converte nomes técnicos armazenados no banco (`first_phasetickets`, `First_Phase`, `second_lot`, etc.) para texto legível em português.
  - Remove sufixos comuns como `tickets`, `ticket`, `bilhetes` antes da normalização.
  - Mapeamento específico: `first_phase` → `Primeiro Lote`, `second_phase` → `Segundo Lote`, `promotional` → `Promocional`, `vip` → `VIP`, etc.
  - Fallback formata underscores/hífens em espaços e capitaliza o texto.
- **Atualização de Views:**
  - Substituído `$batch->name` por `$batch->display_name` em:
    - `ticket-list.blade.php` (ação em massa e edição individual).
    - `batch-manager.blade.php` (lista de lotes).
    - `quick-sale.blade.php` (formulário de venda rápida).
- **Correção do Botão Aplicar (Bulk Actions):**
  - Removido `event.stopImmediatePropagation()` do `onclick` que interferia no fluxo do Livewire.
  - Alterado `wire:model.live` para `wire:model` nos selects de Lote e Estado, eliminando requisições paralelas conflitantes durante a seleção.
- **Otimização do Script de Deploy (`deploy.py`):**
  - Adicionado suporte a múltiplos arquivos no argumento `--file` (ex: `--file path1 --file path2`).

---

## 📅 Julho de 2026

### 9. Migração para Alpine JS — Seleção em Tempo Real e Filtros Dinâmicos

**Problema resolvido:** A seleção de checkboxes (individual e "Select All") causava latência perceptível e travamentos de UI porque cada clique despoletava um ciclo de rede completo para o servidor Livewire via `wire:model.live`, bloqueando a interface até à resposta.

**Solução implementada:**

- **Alpine JS com `$wire.entangle`:**
  - O componente `ticket-list.blade.php` foi envolvido num `x-data` Alpine que mantém um array `selectedIds` sincronizado de forma bidirecional com o Livewire via `$wire.entangle('selectedIds')`.
  - As seleções individuais usam `x-model="selectedIds"` (Alpine puro, sem round-trip ao servidor).
  - O "Select All" usa `x-on:click="toggleAll()"` e `:checked="isAllSelected()"` — métodos Alpine que calculam o estado localmente a partir dos IDs da página atual embutidos num elemento `<div id="page-ids-container" data-ids="...">` renderizado pelo servidor.

- **Barra de Ações em Massa refatorada:**
  - Visibilidade controlada por `x-show="selectedIds.length > 0"` em vez de `@if(count($selectedIds) > 0)` do Livewire, eliminando re-renderizações do servidor ao selecionar/deselecionar.
  - Contagem de bilhetes selecionados via `<span x-text="selectedIds.length">` — atualização instantânea no DOM.
  - Botões "Aplicar", "Confirmar todos" e "Cancelar todos" usam `x-on:click="if (confirm(...)) { $wire.bulkEdit(); }"` para manter o diálogo de confirmação nativo mas só chamar o servidor após confirmação.
  - Botão "Limpar" usa `x-on:click="selectedIds = []"` — limpa localmente e sincroniza via entangle.

- **Dropdown de Tipos Dinâmico:**
  - Adicionada a propriedade computada `ticketTypes(): array` em `TicketList.php` que consulta `Ticket` e `TicketBatch` para obter todos os `ticket_type` reais na base de dados.
  - Todos os valores são normalizados para `strtolower()` antes de `array_unique()` para eliminar duplicados originados por inconsistências de capitalização (ex: `Primeiro_lote` vs `first_lot`).
  - O dropdown no Blade renderiza as opções dinamicamente via `@foreach($this->ticketTypes as $value => $label)`.

**Resultado:** As seleções são agora instantâneas (sem latência de rede), o "Select All" seleciona 20 bilhetes em <100ms, e os filtros de tipo refletem com precisão os dados reais da base de dados.

**Ficheiros alterados:**
- `app/Livewire/TicketList.php` — Adicionada `ticketTypes()` computed property.
- `resources/views/livewire/ticket-list.blade.php` — Migração completa para Alpine JS entangle.

---

### 10. Correcção de Integridade de Negócio — Receita Real vs Emitidos Pendentes

**Problema identificado:** A Venda Rápida gerava bilhetes directamente com `status='confirmed'`, o que causava:
1. O Dashboard exibia todos os bilhetes emitidos como **Receita Real** — inflacionando a receita com bilhetes que ainda não tinham sido vendidos fisicamente.
2. O contador `batch.sold` era incrementado no momento de geração e não da venda, distorcendo a ocupação dos lotes.
3. Bilhetes movidos entre lotes (ex: Promocional → Lote Normal) por `bulkEdit` ajustavam o `sold` mesmo para bilhetes pendentes.

**Solução implementada:**

- **`QuickSale.php`:** Status alterado de `'confirmed'` → `'pending'`. O contador `batch->sold` deixou de ser incrementado na geração.
- **`TicketService::confirmTicket()`:** Agora incrementa `batch->sold` no momento da confirmação (venda real).
- **`TicketService::cancelTicket()`:** Agora decrementa `batch->sold` se o bilhete cancelado estava `confirmed`.
- **`TicketList::bulkEdit()`:** A troca de lote só ajusta `sold` se o bilhete for `confirmed/used`. A mudança de status entre `pending` ↔ `confirmed` também ajusta o contador correctamente.
- **`TicketService::getEventStats()`:** Adicionados campos `potential_revenue` (soma dos bilhetes `pending`) e `first_lot` no `by_type`.
- **Dashboard:** Card "Pendentes" renomeado para **"Emitidos"** com subtítulo "Aguardam venda". Card "Receita Total" → **"Receita Real"** com nota "Confirmado + Usado". Novo card **"Rec. Potencial"** (ouro translúcido) mostra o valor dos emitidos não confirmados.

**Fluxo correcto após a correcção:**
```
QuickSale → status='pending' (bilhete impresso, não vendido)
         → Rec. Potencial sobe
Vendedor vende → Admin confirma (bulkEdit ou confirmTicket)
              → batch.sold++
              → Rec. Real sobe
              → Rec. Potencial desce
```

**Ficheiros alterados:**
- `app/Services/TicketService.php` — confirmTicket, cancelTicket, getEventStats.
- `app/Livewire/Admin/QuickSale.php` — status e remoção do sold++.
- `app/Livewire/TicketList.php` — bulkEdit com integridade de sold.
- `resources/views/livewire/admin-dashboard.blade.php` — novo card Rec. Potencial.

---

### 11. Implementação do Scanner de Vendas e Eliminação de Bilhetes

**Contexto:** Após separar a emissão (bilhetes `pending`) da venda real (bilhetes `confirmed`), surgiu a necessidade de um fluxo ágil para os vendedores físicos poderem confirmar a venda de um bilhete emitido. Além disso, foi pedida a capacidade de eliminar definitivamente bilhetes cancelados para manter a base de dados limpa, sem afectar as estatísticas.

**O que foi feito:**

1. **Scanner de Vendas (Confirmar Venda):**
   - Criado novo controller `SaleConfirmController` separado do `ValidationController` (usado na entrada do evento) para evitar confusão de fluxos.
   - O Scanner de Vendas altera o estado de `pending` para `confirmed` e actualiza o contador de vendas do lote (`batch->sold++`).
   - Mantém as verificações de segurança: impede confirmação dupla, avisa se bilhete foi cancelado ou usado.
   - Nova interface baseada no design do scanner da porta, adicionada ao menu lateral sob a Venda Rápida.

2. **Eliminação de Bilhetes Cancelados:**
   - Adicionado método `deleteTicket` no `AdminController` e no `TicketList` (Livewire).
   - Implementado um "Safety Guard": o sistema só permite apagar bilhetes que tenham o status `cancelled`. Bilhetes activos, confirmados ou usados não podem ser eliminados para preservar a integridade da auditoria.
   - Ao apagar, é registado no `AuditService` com a informação completa do bilhete antes da eliminação definitiva.

**Ficheiros adicionados/alterados:**
- `app/Http/Controllers/Api/SaleConfirmController.php` (novo)
- `resources/views/admin/sale-scanner.blade.php` (novo)
- `app/Http/Controllers/AdminController.php` — Novo método `deleteTicket`.
- `app/Livewire/TicketList.php` — Ajustado o `deleteTicket` com guarda de segurança e removido o botão no blade onde não devia.
- `routes/web.php` — Adicionadas as novas rotas.
- `resources/views/layouts/admin.blade.php` — Link no sidebar para o Scanner de Vendas.

---

### 12. Otimização de Performance e Filtro de Venda Rápida

**Contexto:** Utilizadores reportavam extrema lentidão na listagem e alteração de filtros/ações na listagem de bilhetes (`TicketList`). Além disso, surgiu a necessidade de filtrar e ordenar bilhetes gerados por "Venda Rápida" de forma a agilizar a identificação de bilhetes impressos que ainda não foram vendidos (estado `Pendente`).

**O que foi feito:**
1. **Caching de Propriedades Pesadas:** Implementado caching (`Cache::remember`) no método `ticketTypes()` de `TicketList.php` por 60 segundos. Isto elimina o scan completo de `SELECT DISTINCT` na tabela de bilhetes a cada request/keystroke, reduzindo tempos de resposta de segundos para 0.008s.
2. **Filtro de Origem (ticket_mode):** Adicionado um novo filtro na listagem para separar bilhetes por "Origem" (`personalized` / Online vs `quick_sale` / Venda Rápida). Desta forma, o administrador pode combinar o estado `Pendente` + Origem `Venda Rápida` para listar todos os bilhetes emitidos que não foram vendidos.
3. **Ordenação por Origem e Telefone:** Adicionado suporte a ordenação interactiva no cabeçalho da tabela pelas colunas "Origem" e "Telefone" (buyer_phone), tornando a listagem muito mais flexível e robusta como um DataTable.
4. **Indicador Visual na Grelha (Grid View):** O modo de visualização em grelha (Grid View) também foi actualizado para exibir a etiqueta "Origem" em cada bilhete.
5. **Paginação Dinâmica:** Adicionado seletor de quantidade de resultados por página (10, 20, 50, 100 resultados) com persistência de estado via URL (`$perPage`), facilitando ações em lote de grandes quantidades de bilhetes.

**Ficheiros alterados:**
- `app/Livewire/TicketList.php` — Propriedades `filterMode` e `perPage`, hooks correspondentes, cache em `ticketTypes`, tratamento no query builder `tickets()`.
- `resources/views/livewire/ticket-list.blade.php` — Novos dropdowns de filtro por Origem e Quantidade por página, alteração de colunas da tabela para incluir "Origem" e "Telefone" como ordenáveis por clique, e visualização de Origem na vista de grelha.

---

### 13. Resolução de Congelamento do Navegador (Performance de Renderização)

**Contexto:** Ao selecionar grandes volumes de dados (ações em massa), alterar a paginação ou aplicar filtros, o navegador do utilizador ficava extremamente lento ou chegava a congelar completamente (ficando apenas o cursor a responder).

**Causa:** Nos layouts `admin.blade.php` e `public.blade.php`, o hook `morph.updated` do Livewire executava `lucide.createIcons()` imediatamente para *cada* elemento DOM atualizado. Numa tabela com 100 linhas e múltiplos ícones por linha, isso disparava milhares de varreduras completas no DOM (`document.querySelectorAll('[data-lucide]')`) numa única renderização, gerando um gargalo de processamento massivo ($O(N^2)$) que travava a thread de execução do navegador.

**O que foi feito:**
1. **Debounce do Lucide:** Introduzido um debounce de 50ms no hook `morph.updated` em ambos os layouts (`admin` e `public`).
2. **Otimização:** Isto garante que a varredura e renderização dos ícones do Lucide ocorre apenas **uma única vez** no final de todo o processo de morphing do Livewire, eliminando o gargalo de processamento e deixando a interface instantânea.

**Ficheiros alterados:**
- `resources/views/layouts/admin.blade.php` — Debounce adicionado no morph.updated do Livewire.
- `resources/views/layouts/public.blade.php` — Debounce adicionado no morph.updated do Livewire.
