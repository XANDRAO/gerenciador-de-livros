<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class BrasilAPIService
{
    private $baseUrl = 'https://brasilapi.com.br/api';

    public function getAddressByCep($cep)
    {
        $url = "{$this->baseUrl}/cep/v1/{$cep}";
        $response = Http::withoutVerifying()->get($url); // Desativa a verificação SSL

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
