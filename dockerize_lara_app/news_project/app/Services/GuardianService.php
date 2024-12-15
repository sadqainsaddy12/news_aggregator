<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GuardianService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = 'https://content.guardianapis.com/';
        $this->apiKey = config('services.api_key.the_guardian');
    }

    /**
     * Fetch articles from The Guardian API.
     *
     * @param array $params Query parameters for the API request.
     * @return array Response data or an error message.
     */
    public function fetchArticles(array $params = [])
    {
        $queryParams = array_merge($params, [
            'api-key' => $this->apiKey,
        ]);

        $response = Http::get($this->baseUrl . 'search', $queryParams);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => true,
            'message' => $response->body(),
        ];
    }
}
