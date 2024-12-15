<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;

class NYTService
{
    protected $apiKey;

    public function __construct()
    {
        // Replace with your actual NYT API key
        $this->apiKey = config('services.api_key.new_york_time');

    }

   
    public function fetchArticles($sectionName = null, $pubDate = null)
    {
        // Construct the filter query
        $filters = [];
        
        if ($sectionName) {
            $filters[] = 'section_name:("' . $sectionName . '")';
        }

        // if ($source) {
        //     $filters[] = 'source:("' . $source . '")';
        // }

        // if ($author) {
        //     $filters[] = 'byline:("' . $author . '")';
        // }

        if ($pubDate) {
            $filters[] = 'pub_date:("2024-12-13")';
        }

        $filterQuery = implode(' AND ', $filters);  // Join all filters with AND

        // Make the request to the API
        $response = Http::get('https://api.nytimes.com/svc/search/v2/articlesearch.json', [
            'fq' => $filterQuery,   // Apply filters
            'api-key' => $this->apiKey
        ]);

        // Check if the response was successful
        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}