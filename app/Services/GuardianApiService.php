<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuardianApiService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.guardian.key');
        $this->baseUrl = config('services.guardian.base_url');
    }

    public function fetchArticles(array $params)
    {
        $response = Http::withOptions(['verify' => false])
            ->get("{$this->baseUrl}/search", array_merge($params, [
                'api-key' => $this->apiKey,
            ]));

        if ($response->failed()) {
            throw new \Exception('The Guardian request failed: ' . $response->body());
        }
        if ($response->failed()) {
            Log::error('Error fetching articles from The Guardian: ' . $response->body());
            throw new \Exception('The Guardian request failed: ' . $response->body());
        }
    
        Log::info('Fetched articles successfully from The Guardian', ['articles_count' => count($response->json()['articles'] ?? [])]);

        return $response->json()['response']['results'];
    }
}