<?php
namespace App\Services;

use App\Models\Ticket;
use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private $sms;
    private string $senderId;

    public function __construct()
    {
        $username = config('services.africastalking.username', 'sandbox');
        $apiKey = config('services.africastalking.api_key');
        
        if (!$apiKey) {
            Log::warning("AfricasTalking API Key is missing.");
        } else {
            try {
                $at = new AfricasTalking($username, $apiKey);
                $this->sms = $at->sms();
            } catch (\Exception $e) {
                Log::error("Failed to initialize AfricasTalking: " . $e->getMessage());
            }
        }
        
        $this->senderId = config('services.africastalking.sender_id', '');
    }

    /**
     * Formatar número para +258XXXXXXXXX
     */
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
        if (str_starts_with($phone, '0')) $phone = '258' . substr($phone, 1);
        if (!str_starts_with($phone, '+')) $phone = '+' . ltrim($phone, '+');
        return $phone;
    }

    /**
     * Enviar SMS simples
     */
    public function send(string $phone, string $message): bool
    {
        if (!$this->sms) {
            Log::error("SMS Service not initialized properly. Cannot send SMS to $phone");
            return false;
        }

        try {
            $options = [
                'to'      => $this->formatPhone($phone),
                'message' => $message,
            ];
            
            if ($this->senderId && $this->senderId !== 'sandbox') {
                $options['from'] = $this->senderId;
            }

            $result = $this->sms->send($options);

            $recipients = $result['data']->SMSMessageData->Recipients ?? [];
            $success    = collect($recipients)->every(fn($r) => $r->status === 'Success');

            Log::info("SMS enviado para $phone — status: " . ($success ? 'ok' : 'falhou'));
            return $success;

        } catch (\Exception $e) {
            Log::error("SMS falhou para $phone: " . $e->getMessage());
            return false;
        }
    }

    /**
     * SMS de reserva pendente (instruções M-Pesa)
     */
    public function sendPaymentPending(Ticket $ticket): bool
    {
        if (!$ticket->buyer_phone) return false;

        $msg  = "RENUNCIA 11JUL2026\n";
        $msg .= "Reserva: {$ticket->ticket_code}\n";
        $msg .= "Tipo: {$ticket->ticket_type} - {$ticket->price}MT\n";
        $msg .= "Pague via M-Pesa: 87541644\n";
        $msg .= "Ref: RENUNCIA " . strtoupper(substr($ticket->buyer_name, 0, 8));
        // Manter <= 160 chars para 1 SMS

        return $this->send($ticket->buyer_phone, $msg);
    }

    /**
     * SMS de confirmação (bilhete confirmado)
     */
    public function sendConfirmation(Ticket $ticket): bool
    {
        if (!$ticket->buyer_phone) return false;

        $msg  = "BILHETE CONFIRMADO\n";
        $msg .= "Concerto Renuncia 11/07/2026\n";
        $msg .= "Codigo: {$ticket->ticket_code}\n";
        $msg .= "Tipo: {$ticket->ticket_type}\n";
        $msg .= "Apresente na entrada\n";
        $msg .= "Info: 87541644";

        return $this->send($ticket->buyer_phone, $msg);
    }

    /**
     * SMS de lembrete (dia anterior)
     */
    public function sendReminder(Ticket $ticket): bool
    {
        if (!$ticket->buyer_phone) return false;

        $msg  = "AMANHA! Concerto Renuncia\n";
        $msg .= "11 Jul 2026 - 16h00\n";
        $msg .= "Pavilhao do Benfica, Quelimane\n";
        $msg .= "Codigo: {$ticket->ticket_code}";

        return $this->send($ticket->buyer_phone, $msg);
    }

    /**
     * SMS de reserva pendente em massa
     */
    public function sendBulkPaymentPending(array $tickets): bool
    {
        if (empty($tickets) || !$tickets[0]->buyer_phone) return false;

        $msg  = "RENUNCIA 11JUL2026\n";
        $msg .= count($tickets) . " Reservas\n";
        $msg .= "Pague via M-Pesa: 87541644\n";
        $msg .= "Ref: RENUNCIA " . strtoupper(substr($tickets[0]->buyer_name, 0, 8));

        return $this->send($tickets[0]->buyer_phone, $msg);
    }

    /**
     * SMS de confirmação em massa
     */
    public function sendBulkConfirmation(array $tickets): bool
    {
        if (empty($tickets) || !$tickets[0]->buyer_phone) return false;

        $msg  = count($tickets) . " BILHETES CONFIRMADOS\n";
        $msg .= "Concerto Renuncia\n";
        $msg .= "Cods: " . implode(', ', collect($tickets)->pluck('ticket_code')->toArray()) . "\n";
        $msg .= "Info: 87541644";

        return $this->send($tickets[0]->buyer_phone, $msg);
    }
}
