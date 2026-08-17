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
        $driver = env('SMS_DRIVER', 'africastalking');
        if ($driver === 'httpsms') {
            return;
        }

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
        $driver = env('SMS_DRIVER', 'africastalking');

        if ($driver === 'httpsms') {
            return $this->sendViaHttpSms($phone, $message);
        }

        if (!$this->sms) {
            Log::error("SMS Service (AfricasTalking) not initialized properly. Cannot send SMS to $phone");
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

            Log::info("SMS enviado para $phone via AfricasTalking — status: " . ($success ? 'ok' : 'falhou'));
            return $success;

        } catch (\Exception $e) {
            Log::error("SMS falhou para $phone via AfricasTalking: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envia SMS via httpSMS (httpsms.com)
     */
    private function sendViaHttpSms(string $to, string $message): bool
    {
        $apiKey = env('HTTPSMS_KEY');
        $from   = env('HTTPSMS_FROM');

        if (empty($apiKey) || empty($from)) {
            Log::warning('[SMS] httpSMS configuration missing in .env (HTTPSMS_KEY or HTTPSMS_FROM).');
            return false;
        }

        $to = $this->normalizePhone($to);
        if ($to === null) {
            Log::warning("[SMS] Número de telefone inválido ou não reconhecido: {$to}");
            return false;
        }

        $from = $this->normalizePhone($from);
        if ($from === null) {
            Log::warning('[SMS] Número de origem (HTTPSMS_FROM) inválido no .env.');
            return false;
        }

        $url  = 'https://api.httpsms.com/v1/messages/send';
        $body = json_encode([
            'content' => $message,
            'from'    => $from,
            'to'      => $to,
        ]);

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $body,
                CURLOPT_HTTPHEADER     => [
                    'x-api-key: '    . $apiKey,
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT        => 10,
            ]);

            $response = curl_exec($ch);

            if ($response === false) {
                $error = curl_error($ch);
                curl_close($ch);
                Log::error('[SMS] cURL error (httpSMS): ' . $error);
                return false;
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($httpCode >= 200 && $httpCode < 300) {
                Log::info("[SMS] Mensagem enviada com sucesso via httpSMS para {$to}.");
                return true;
            }

            $detail = $result['message'] ?? $response;
            if (is_array($detail) || is_object($detail)) {
                $detail = json_encode($detail, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            Log::error("[SMS] httpSMS error HTTP {$httpCode}: {$detail}");
            return false;

        } catch (\Throwable $e) {
            Log::error('[SMS] Exception (httpSMS): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Normaliza um número de telefone para o formato E.164.
     */
    private function normalizePhone(string $phone): ?string
    {
        $phone = preg_replace('/[\s\-().]+/', '', $phone);

        if (preg_match('/^\+\d{8,15}$/', $phone)) {
            return $phone;
        }

        if (preg_match('/^(8[2-7])\d{7}$/', $phone)) {
            return '+258' . $phone;
        }

        if (preg_match('/^258(8[2-7]\d{7})$/', $phone)) {
            return '+' . $phone;
        }

        if (preg_match('/^\d{8,15}$/', $phone)) {
            return '+' . $phone;
        }

        return null;
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
