<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    private string $apiKey;
    private string $baseUrl;
    private string $serviceProviderCode;

    public function __construct()
    {
        $this->apiKey = config('services.mpesa.api_key', '');
        $this->baseUrl = config('services.mpesa.base_url', 'https://api.vm.co.mz');
        $this->serviceProviderCode = config('services.mpesa.service_provider_code', '');
    }

    /**
     * Initiate a C2B (Customer to Business) payment.
     *
     * @param string $phone Customer phone number (e.g., 258841234567)
     * @param float $amount Amount in MZN
     * @param string $reference Transaction reference
     * @param string $thirdPartyReference Additional reference
     * @return array
     */
    public function initiatePayment(string $phone, float $amount, string $reference, string $thirdPartyReference): array
    {
        if (empty($this->apiKey)) {
            Log::warning('M-Pesa API key not configured. Payment simulation mode.');
            return [
                'success' => false,
                'message' => 'M-Pesa não configurado. Configure MPESA_API_KEY no .env',
                'simulation' => true,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
                'Origin' => config('app.url'),
            ])->post("{$this->baseUrl}/ipg/v1x/c2bPayment/singleStage/", [
                'input_TransactionReference' => $reference,
                'input_CustomerMSISDN' => $phone,
                'input_Amount' => $amount,
                'input_ThirdPartyReference' => $thirdPartyReference,
                'input_ServiceProviderCode' => $this->serviceProviderCode,
            ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('M-Pesa payment error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao processar pagamento M-Pesa.',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a payment status.
     */
    public function verifyPayment(string $reference): array
    {
        // Placeholder for payment verification
        Log::info("M-Pesa payment verification requested for ref: {$reference}");

        return [
            'success' => false,
            'message' => 'Verificação de pagamento ainda não implementada.',
        ];
    }

    /**
     * Handle M-Pesa callback.
     */
    public function handleCallback(array $data): array
    {
        Log::info('M-Pesa callback received', $data);

        return [
            'processed' => true,
            'data' => $data,
        ];
    }
}
