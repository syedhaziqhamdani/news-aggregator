<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
        // Create a unique cache key based on the API parameters
        $cacheKey = 'nyt_articles:' . md5(json_encode($params));

        // Check and store the response in cache for 60 minutes
        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($params) {
            try {
                // Make the API request
                $response = Http::withOptions(['verify' => false])
                    ->get("{$this->baseUrl}/articlesearch.json", array_merge($params, [
                        'api-key' => $this->apiKey,
                    ]));

                // Handle response failures
                if ($response->failed()) {
                    Log::error('Error fetching articles from New York Times: ' . $response->body());
                    throw new \Exception('NYT API request failed: ' . $response->body());
                }

                $responseData = $response->json();

                // Validate response structure
                if (!isset($responseData['response']['docs'])) {
                    Log::error('Unexpected response structure from NYT API', ['response' => $responseData]);
                    throw new \Exception('Unexpected NYT API response structure.');
                }

                // Log the successful fetch
                $articlesCount = count($responseData['response']['docs']);
                Log::info('Fetched articles successfully from New York Times', ['articles_count' => $articlesCount]);

                // Map the response to a consistent structure
                return collect($responseData['response']['docs'])->map(function ($article) {
                    return [
                        'title' => $article['headline']['main'] ?? 'No Title',
                        'description' => $article['abstract'] ?? 'No Description',
                        'source' => 'The New York Times',
                        'url' => $article['web_url'] ?? '',
                        'publishedAt' => $article['pub_date'] ?? '',
                    ];
                })->toArray();
            } catch (\Exception $e) {
                Log::critical('An unexpected error occurred in NytApiService', [
                    'error_message' => $e->getMessage(),
                    'params' => $params,
                ]);
                throw $e;
            }
        });
    }
}
