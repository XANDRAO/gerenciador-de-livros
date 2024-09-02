<?php

namespace App\Http\Controllers;

use App\Http\Services\BrasilAPIService; 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CepController extends Controller
{
    private $brasilAPIService;

    public function __construct(BrasilAPIService $brasilAPIService)
    {
        $this->brasilAPIService = $brasilAPIService;
    }

    public function index($cep)
    {
        $address = $this->brasilAPIService->getAddressByCep($cep);
        
        if (!$address) {
            return response()->json(['error' => 'Invalid CEP or unable to fetch address'], 400);
        }

        return response()->json($address);
    }
}
