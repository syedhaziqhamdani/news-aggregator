<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class TheGuardianService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.theguardian.key'); // Store API key in config/services.php
    }

    public function fetchArticles(array $params)
    {
        $response = Http::get('https://content.guardianapis.com/search', array_merge($params, [
            'api-key' => $this->apiKey,
        ]));

        if ($response->failed()) {
            throw new \Exception('The Guardian request failed: ' . $response->body());
        }

        return $response->json();
    }
}
