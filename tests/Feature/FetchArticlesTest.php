<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Article;

class FetchArticlesTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_articles_are_fetched_and_stored()
    {
        $this->artisan('fetch:articles')
             ->expectsOutput('Fetching articles...')
             ->expectsOutput('Articles fetched and stored successfully.')
             ->assertExitCode(0);

        $this->assertDatabaseCount('articles', 10);
    }
}
