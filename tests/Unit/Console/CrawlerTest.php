<?php

namespace Tests\Unit\Console;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CrawlerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function crawler_accepts_a_url_and_pages(): void
    {
        $url = 'https://agencyanalytics.com';

        $this
            ->artisan('app:crawl', [
                '--url' => $url,
                '--pages' => '1',
            ])
            ->expectsOutput('Starting to crawl')
            ->expectsOutput('Crawling ' . $url)
            ->expectsOutput('Finished crawling');
    }
}
