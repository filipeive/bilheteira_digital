# Bilheteira Digital - Concerto Renúncia

Sistema de venda e gestão de bilhetes para eventos com QR Code.

## Características

- **Venda de bilhetes** online com M-Pesa, e-Mola, dinheiro ou cortesia
- **QR Code** único para cada bilhete
- **Upload manual** para bilhetes presenciais/cortesias
- **Scanner** para validação na entrada do evento
- **Dashboard administrativo** com estatísticas e gestão de bilhetes
- **Notificações** via WhatsApp e email (configurável)

## Requisitos

- PHP 8.3+
- MySQL/PostgreSQL
- Node.js 18+
- Extensões: GD, Zip, OpenSSL

## Instalação

```bash
git clone git@github.com:filipeive/bilheteira_digital.git
cd bilheteira_digital

composer install
npm install

cp .env.example .env
php artisan key:generate

# Configurar .env com database e mail settings
php artisan migrate --seed
npm run build
```

## Configuração

### WhatsApp API (opcional)

Adicionar ao `.env`:
```
SERVICES_WHATSAPP_TOKEN=seu_token
SERVICES_WHATSAPP_PHONE_ID=seu_phone_id
```

### Mail

Configurar SMTP no `.env` para envio de bilhetes por email.

## Utilização

- **Site público**: `/` - Página de venda de bilhetes
- **Consulta**: `/consultar` - Consultar bilhetes pelo telefone
- **Admin**: `/admin` - Dashboard (requer login como admin/organizer)
- **Scanner**: `/validar` - Validação de bilhetes (requer login)

## Roles

- `admin` - Acesso total
- `organizer` - Acesso ao scanner e relatórios
- `validator` - Apenas scanner

## Licença

MIT