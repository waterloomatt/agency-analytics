<?php

namespace Tests\Unit\Console;

use App\Enums\CrawlStatus;
use App\Models\Crawl;
use Tests\TestCase;

class CrawlerTest extends TestCase
{
    /** @test */
    public function crawler_accepts_an_existing_crawl(): void
    {
        $crawl = Crawl::create([
            'status' => CrawlStatus::RUNNING,
            'url' => 'https://agencyanalytics.com',
            'pages' => 1,
        ]);

        $this
            ->artisan('app:crawl', ['--crawl' => $crawl->id])
            ->expectsOutput('Starting to crawl')
            ->expectsOutput('Crawling ' . $crawl->url)
            ->expectsOutput('Finished crawling');
    }

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
