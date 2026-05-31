<?php

namespace App\Services;

use App\Models\Ticket;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\Process\Process;

class QrCodeService
{
    private string $secret;

    public function __construct()
    {
        $this->secret = config('app.key');
    }

    /**
     * Generate the payload string for a ticket QR code.
     */
    public function generatePayload(Ticket $ticket): string
    {
        return "{$ticket->ticket_code}|{$ticket->buyer_name}|{$ticket->ticket_type}";
    }

    /**
     * Sign a payload with HMAC-SHA256.
     */
    public function signPayload(string $payload): string
    {
        return hash_hmac('sha256', $payload, $this->secret);
    }

    /**
     * Verify a signature against a payload.
     */
    public function verifySignature(string $payload, string $signature): bool
    {
        $expected = $this->signPayload($payload);
        return hash_equals($expected, $signature);
    }

    /**
     * Generate the full signed QR payload for storage.
     */
    public function generateSignedPayload(Ticket $ticket): string
    {
        $payload = $this->generatePayload($ticket);
        $signature = $this->signPayload($payload);
        return "{$payload}|{$signature}";
    }

    /**
     * Parse and verify a scanned QR code payload.
     *
     * @return array{valid: bool, ticket_code: string|null, buyer_name: string|null, ticket_type: string|null}
     */
    public function parseAndVerify(string $qrContent): array
    {
        $parts = explode('|', $qrContent);

        if (count($parts) !== 4) {
            return ['valid' => false, 'ticket_code' => null, 'buyer_name' => null, 'ticket_type' => null];
        }

        [$ticketCode, $buyerName, $ticketType, $signature] = $parts;
        $payload = "{$ticketCode}|{$buyerName}|{$ticketType}";

        return [
            'valid' => $this->verifySignature($payload, $signature),
            'ticket_code' => $ticketCode,
            'buyer_name' => $buyerName,
            'ticket_type' => $ticketType,
        ];
    }

    /**
     * Generate QR code image as SVG string.
     */
    public function generateQrSvg(Ticket $ticket, int $size = 300): string
    {
        return QrCode::size($size)
            ->errorCorrection('H')
            ->generate($ticket->qr_payload);
    }

    /**
     * Generate QR code image as PNG for PDF embedding.
     */
    public function generateQrPng(Ticket $ticket, int $size = 300): string
    {
        $pythonQr = $this->generateQrPngWithPython($ticket->qr_payload, $size);

        if ($pythonQr !== null) {
            return $pythonQr;
        }

        return QrCode::format('png')
            ->size($size)
            ->errorCorrection('H')
            ->generate($ticket->qr_payload);
    }

    private function generateQrPngWithPython(string $payload, int $size): ?string
    {
        $script = base_path('scripts/generate_qr.py');

        if (!is_file($script)) {
            return null;
        }

        $logoPath = public_path('alpha-logo-gold.png');

        $cmd = [
            'python3',
            $script,
            '--payload-b64',
            base64_encode($payload),
            '--size',
            (string) $size,
        ];

        if (is_file($logoPath)) {
            $cmd[] = '--logo-path';
            $cmd[] = $logoPath;
        }

        $process = new Process($cmd);
        $process->setTimeout(10);
        $process->run();

        if (!$process->isSuccessful() || $process->getOutput() === '') {
            \Illuminate\Support\Facades\Log::warning('Python QR failed: ' . $process->getErrorOutput());
            return null;
        }

        return $process->getOutput();
    }
}
