<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewsApiService
{
    protected $baseUrl = 'https://newsapi.org/v2';
    protected $apiKey;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key');
        //
    }
}
