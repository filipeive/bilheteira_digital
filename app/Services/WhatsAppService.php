<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private ?string $token;
    private ?string $phoneId;

    public function __construct()
    {
        $this->token   = config('services.whatsapp.token');
        $this->phoneId = config('services.whatsapp.phone_id');
    }

    /**
     * Check if WhatsApp API is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->token) && !empty($this->phoneId);
    }

    /**
     * Format phone number to international +258 format.
     */
    public function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '258')) {
            return '+' . $phone;
        }

        if (str_starts_with($phone, '8') && strlen($phone) >= 9) {
            return '+258' . $phone;
        }

        return '+258' . $phone;
    }

    /**
     * Send ticket confirmation via WhatsApp.
     */
    public function sendTicketConfirmation(Ticket $ticket): bool
    {
        if (!$ticket->buyer_phone || !$this->isConfigured()) {
            return false;
        }

        $message = "🎫 *Bilhete Confirmado!*\n\n"
            . "Olá {$ticket->buyer_name}!\n"
            . "O seu bilhete para o *Concerto Renúncia* está confirmado.\n\n"
            . "📋 Código: `{$ticket->ticket_code}`\n"
            . "🎟 Tipo: {$ticket->getTicketTypeLabel()}\n"
            . "📅 11 Julho 2026 · 16:00\n"
            . "📍 Pavilhão do Benfica, Quelimane\n\n"
            . "Guarde este código. Será necessário na entrada.\n"
            . "Obrigado pela sua compra! 🎶";

        $result = $this->sendText($ticket->buyer_phone, $message);

        if ($result) {
            $ticket->update(['whatsapp_sent_at' => now()]);
        }

        return $result;
    }

    /**
     * Send payment pending notification via WhatsApp.
     */
    public function sendPaymentPending(Ticket $ticket): bool
    {
        if (!$ticket->buyer_phone || !$this->isConfigured()) {
            return false;
        }

        $message = "⏳ *Pagamento Pendente*\n\n"
            . "Olá {$ticket->buyer_name}!\n"
            . "O seu bilhete `{$ticket->ticket_code}` está reservado "
            . "mas aguarda confirmação de pagamento.\n\n"
            . "💰 Valor: {$ticket->price} MZN\n"
            . "📱 M-Pesa/e-Mola para confirmação\n\n"
            . "Após pagamento, envie o comprovativo para que possamos confirmar.";

        return $this->sendText($ticket->buyer_phone, $message);
    }

    /**
     * Send event reminder via WhatsApp.
     */
    public function sendEventReminder(Ticket $ticket): bool
    {
        if (!$ticket->buyer_phone || !$this->isConfigured()) {
            return false;
        }

        $message = "🔔 *Lembrete: Concerto Renúncia é AMANHÃ!*\n\n"
            . "Olá {$ticket->buyer_name}!\n"
            . "Não se esqueça — o evento é amanhã!\n\n"
            . "📋 Bilhete: `{$ticket->ticket_code}`\n"
            . "📅 11 Julho 2026 · 16:00\n"
            . "📍 Pavilhão do Benfica, Quelimane\n\n"
            . "Traga o seu bilhete (digital ou impresso). Até lá! 🎶";

        return $this->sendText($ticket->buyer_phone, $message);
    }

    /**
     * Send a plain text message via WhatsApp Cloud API.
     */
    public function sendText(string $phone, string $message): bool
    {
        if (!$this->isConfigured()) {
            Log::info("WhatsApp not configured. Message to {$phone} not sent.");
            return false;
        }

        $formattedPhone = $this->formatPhone($phone);

        try {
            $response = Http::withToken($this->token)
                ->post("https://graph.facebook.com/v18.0/{$this->phoneId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to'                => $formattedPhone,
                    'type'              => 'text',
                    'text'              => ['body' => $message],
                ]);

            if ($response->successful()) {
                Log::info("WhatsApp sent to {$formattedPhone}");
                return true;
            }

            Log::warning("WhatsApp failed for {$formattedPhone}: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp exception for {$formattedPhone}: " . $e->getMessage());
            return false;
        }
    }
}
