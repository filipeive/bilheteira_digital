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
