<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewsApiService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key'); // Store API key in config/services.php
        $this->baseUrl = config('services.newsapi.base_url'); // Store base URL in config/services.php
    }

    public function fetchArticles(array $params)
    {
        // Make the HTTP request
        $response = Http::withOptions(['verify' => false]) // Disable SSL certificate verification
            ->get("{$this->baseUrl}/everything", array_merge($params, [
                'apiKey' => $this->apiKey,
            ]));

        // Check if the request failed
        if ($response->failed()) {
            throw new \Exception('NewsAPI request failed: ' . $response->body());
        }

        return $response->json();
    }
}