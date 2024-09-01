<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Http\Services\GoogleBooksService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CepController
{
    private $baseUrl = 'http://brasilapi.com.br/api';

    private $brasilAPIService;

    public function __construct(BrasilAPIService $brasilAPIService)
    {
        $this->brasilAPIService = $brasilAPIService;
    } 
    
    public function index(){
        $address = $this->brasilAPIService->getAddressByCep($validated['cep']);
        
        if (!$address) {
            return response()->json(['error' => 'Invalid CEP or unable to fetch address'], 400);
    }
}

}