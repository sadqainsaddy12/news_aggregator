<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewsAPIService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = 'https://newsapi.org/v2';
        $this->apiKey = config('services.api_key.news');
    }

    /**
     * Fetch articles based on the given query.
     */
    public function fetchArticles($query, $params = [])
    {
        $endpoint = $this->baseUrl . '/everything';

        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->get($endpoint, array_merge(['q' => $query], $params));

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => true,
            'message' => $response->body(),
        ];
    }

    
}
