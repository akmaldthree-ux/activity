<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->apiKey = config('services.fonnte.api_key', '');
    }

    public function send(string $phone, string $message): bool
    {
        if (empty($this->apiKey)) {
            Log::info('[WA Skip] No Fonnte API key. To: ' . $phone);
            return false;
        }

        // Normalize phone: strip leading 0, add 62
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        try {
            $response = Http::withHeaders(['Authorization' => $this->apiKey])
                ->post($this->baseUrl, [
                    'target'  => $phone,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                Log::info('[WA Sent] ' . $phone);
                return true;
            }

            Log::warning('[WA Failed] ' . $phone . ': ' . $response->body());
        } catch (\Throwable $e) {
            Log::error('[WA Error] ' . $e->getMessage());
        }

        return false;
    }
}
