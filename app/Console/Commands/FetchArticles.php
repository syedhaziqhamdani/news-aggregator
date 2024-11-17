<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ArticleAggregatorService;
use App\Models\Article;
use Carbon\Carbon;

class FetchArticles extends Command
{
    protected $signature = 'fetch:articles';
    protected $description = 'Fetch articles from multiple news sources';

    protected $articleAggregatorService;

    public function __construct(ArticleAggregatorService $articleAggregatorService)
    {
        parent::__construct();
        $this->articleAggregatorService = $articleAggregatorService;
    }

    public function handle()
    {
        $this->info('Fetching articles...');
        $params = [
            'q' => 'technology',
            'language' => 'en',
            'pageSize' => 10,
        ];

        try {
            $articles = $this->articleAggregatorService->fetchAllArticles($params);

            foreach ($articles as $articleData) {
                // Skip articles without a valid URL
                if (empty($articleData['url'])) {
                    $this->warn('Skipping article with missing URL.');
                    continue;
                }

                $publishedAt = isset($articleData['publishedAt']) 
                    ? Carbon::parse($articleData['publishedAt'])->toDateTimeString() 
                    : null;

                Article::updateOrCreate(
                    ['url' => $articleData['url']],
                    [
                        'title' => $articleData['title'] ?? 'Untitled',
                        'description' => $articleData['description'] ?? '',
                        'source' => $articleData['source']['name'] ?? '',
                        'category' => 'General',
                        'published_at' => $publishedAt,
                    ]
                );
            }

            $this->info('Articles fetched and stored successfully!');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function shouldBeScheduled(Schedule $schedule): void
    {
        $schedule->command($this->signature)->hourly();
    }
}
