<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NytApiService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.nyt.key');
        $this->baseUrl = config('services.nyt.base_url');
    }

    public function fetchArticles(array $params)
    {
        $response = Http::withOptions(['verify' => false])
            ->get("{$this->baseUrl}/articlesearch.json", array_merge($params, [
                'api-key' => $this->apiKey,
            ]));

        if ($response->failed()) {
            throw new \Exception('NYT request failed: ' . $response->body());
        }
        if ($response->failed()) {
            Log::error('Error fetching articles from NewYork Times: ' . $response->body());
            throw new \Exception('NewYork Times request failed: ' . $response->body());
        }
    
        Log::info('Fetched articles successfully from NewYork Times', ['articles_count' => count($response->json()['articles'] ?? [])]);
        return $response->json()['response']['docs'];
    }
}
