<?php

namespace App\Clients;

use App\Models\Communication;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SmsClient
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.sms_provider.base_url');
    }

    public function send(Communication $communication): void
    {
        $response = Http::post($this->baseUrl.'/6da682f4-8ca4-4ba4-89df-2c772abe882c', [
            $communication->toArray(),
        ]);

        if ($response->failed()) {
            throw new RuntimeException(
                "SMS delivery failed for {$communication->recipient}: HTTP {$response->status()}"
            );
        }
    }
}
