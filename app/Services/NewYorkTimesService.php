<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewYorkTimesService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.nytimes.key'); // Store API key in config/services.php
    }

    public function fetchArticles(array $params)
    {
        $response = Http::get('https://api.nytimes.com/svc/search/v2/articlesearch.json', array_merge($params, [
            'api-key' => $this->apiKey,
        ]));

        if ($response->failed()) {
            throw new \Exception('New York Times request failed: ' . $response->body());
        }

        return $response->json();
    }
}
