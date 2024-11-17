<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ArticleAggregatorService;
use App\Models\Article;

class FetchArticles extends Command
{
    protected $articleAggregatorService;

    /**
     * Command signature
     */
    protected $signature = 'fetch:articles';

    /**
     * Command description
     */
    protected $description = 'Fetch and store articles from various news APIs';

    /**
     * Constructor
     */
    public function __construct(ArticleAggregatorService $articleAggregatorService)
    {
        parent::__construct();
        $this->articleAggregatorService = $articleAggregatorService;
    }

    /**
     * Command logic
     */
    public function handle()
    {
        try {
            $this->info('Fetching articles...');
            $articles = $this->articleAggregatorService->fetchAllArticles();

            foreach ($articles as $article) {
                Article::updateOrCreate(
                    ['url' => $article['url']],
                    [
                        'title' => $article['title'],
                        'description' => $article['description'] ?? null,
                        'source' => $article['source']['name'] ?? 'Unknown',
                        'category' => $article['category'] ?? 'general',
                    ]
                );
            }

            $this->info('Articles fetched and stored successfully.');
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
