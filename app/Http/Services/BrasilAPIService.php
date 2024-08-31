<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class BrasilAPIService
{
    private $baseUrl = 'https://brasilapi.com.br/api';

    public function getAddressByCep($cep)
    {
        $url = "{$this->baseUrl}/cep/v1/{$cep}";
        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
