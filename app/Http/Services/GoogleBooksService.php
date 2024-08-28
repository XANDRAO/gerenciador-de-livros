<?php

namespace App\Services;

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
        $queryParams = http_build_query([
            'q' => $query,
            'maxResults' => $maxResults,
            'key' => $this->apiKey
        ]);

        $url = "{$this->baseUrl}/volumes?{$queryParams}";

        return $this->makeRequest($url);
    }

    public function getBookById($bookId)
    {
        $url = "{$this->baseUrl}/volumes/{$bookId}?key={$this->apiKey}";

        return $this->makeRequest($url);
    }

    private function makeRequest($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            return ['error' => 'Error fetching data from Google Books API'];
        }

        return json_decode($response, true);
    }
}
