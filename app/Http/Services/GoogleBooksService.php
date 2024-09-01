<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class GoogleBooksService
{
    private $apiKey;
    private $baseUrl = 'https://www.googleapis.com/books/v1';

    public function __construct()
    {
        $this->apiKey = env('GOOGLE_BOOKS_API_KEY');
    }

    public function searchBooks($query, $maxResults = 10)
    {
        $queryParams = [
        'q' => $query,
        'maxResults' => $maxResults,
        'key' => $this->apiKey
    ];

    $url = "{$this->baseUrl}/volumes";

    return $this->makeRequest($url, $queryParams);
}


    public function getBookById($bookId)
    {
        $url = "{$this->baseUrl}/volumes/{$bookId}";
        $queryParams = ['key' => $this->apiKey];

        return $this->makeRequest($url, $queryParams);
    }

    private function makeRequest($url, $queryParams = [])
{
    try {
        $response = Http::withOptions([
            'verify' => false, // Desativa a verificação SSL
        ])->get($url, $queryParams);

        if ($response->failed()) {
            return ['error' => 'Google Books API returned an error: HTTP ' . $response->status()];
        }

        return $response->json();

    } catch (\Exception $e) {
        return ['error' => 'Exception occurred: ' . $e->getMessage()];
    }
}

}
