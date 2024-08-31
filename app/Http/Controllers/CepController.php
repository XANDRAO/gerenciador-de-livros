<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CepController extends Controller
{
    public function index(Request $request)
    {
        $cep = '29155663'; // Substitua pelo CEP que deseja testar

        // URL da API do BrasilAPI
        $url = "https://brasilapi.com.br/api/cep/v1/{$cep}";

        // Fazendo a chamada à API com desativação da verificação SSL
        $response = Http::withoutVerifying()->get($url);

        // Verifica se a resposta foi bem-sucedida
        if ($response->successful()) {
            return response()->json($response->json());
        }

        // Retorna erro se a resposta não for bem-sucedida
        return response()->json(['error' => 'Unable to fetch address'], 400);
    }
}
