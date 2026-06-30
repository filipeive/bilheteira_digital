# 📜 Ritual de Início para Agentes de IA

Este documento define o protocolo de inicialização obrigatório para qualquer agente de inteligência artificial (IA) que venha a trabalhar neste projeto no futuro. O objetivo é garantir o alinhamento técnico completo e evitar redundâncias ou desvios de arquitetura.

---

## 🔮 O Ritual de Alinhamento

> [!IMPORTANT]
> **Antes de realizar qualquer alteração no código ou propor soluções, você DEVE:**
> 1. Ler todos os arquivos de documentação localizados na pasta `docs/`.
> 2. Analisar o estado atual da plataforma cruzando a documentação com o código real.
> 3. Identificar possíveis inconsistências ou desvios entre o código e a documentação.
> 4. Propor um plano de trabalho detalhado para aprovação do usuário antes de programar.

### 💬 Mensagem de Ativação do Ritual
O usuário iniciará a sessão com a seguinte frase ou similar:
> *"Meu amigo Chief Architect, antes de começarmos, lê toda a documentação em docs/, resume o estado atual da plataforma, identifica inconsistências entre o código e a documentação, e propõe o plano de trabalho."*

Ao receber este comando, execute imediatamente o protocolo de leitura e responda com um resumo estruturado contendo:
1. **Estado Atual:** O que está implementado e operacional.
2. **Inconsistências Detectadas:** Qualquer discrepância encontrada entre as especificações dos documentos em `docs/` e a realidade do código.
3. **Plano de Ação Proposto:** Passos lógicos e sequenciais para realizar as melhorias solicitadas.

---

## 🎨 Princípios de Design e UI/UX (Aesthetics Guideline)
- **Tema Gold & Dark:** Mantenha estritamente o contraste luxuoso entre tons de dourado (`#D4AF37` / `#D4A017`), preto profundo e cinzas escuros com fundo translúcido (Glassmorphism).
- **Sem Emojis na Interface de Admin/Público:** Use exclusivamente ícones vetoriais `Lucide` para uma aparência limpa e profissional.
- **Responsividade Total:** Todas as tabelas, botões e formulários criados ou alterados devem comportar-se perfeitamente em dispositivos móveis.
- **Não usar Placeholders:** Sempre que necessitar de imagens reais ou ilustrativas, gere e guarde no armazenamento local correto, evitando campos vazios ou links quebrados.

---

## 🛠️ Padrões Técnicos e Regras de Segurança
1. **Ambiente Local:** O servidor de desenvolvimento roda na porta `8080` (ex: `php artisan serve --port=8080`).
2. **Ações em Massa (Bulk Actions):** Qualquer alteração em lote de bilhetes deve registrar o histórico detalhado utilizando `AuditService::log()` para fins de auditoria e segurança.
3. **Gerenciamento de Lotes:** Sempre que alterar o lote (`batch_id`) de um bilhete, as contagens de `sold` correspondentes aos lotes de origem e destino devem ser atualizadas atomicamente (decrementando o antigo e incrementando o novo).
4. **Symlink do Storage:** As imagens enviadas via painel administrativo vão para o disco público. Garanta a existência do symlink através de `php artisan storage:link`. Em ambientes específicos (como HostGator), siga as diretrizes documentadas em `docs/arquitetura.md`.
