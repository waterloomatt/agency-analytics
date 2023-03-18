<?php

namespace Tests\Unit\Pipelines\Crawler;

use App\Enums\CrawlStatus;
use App\Models\Crawl;
use App\Models\CrawlPage;
use App\Pipelines\Crawler\Pipes\ExternalLinks;
use App\Pipelines\Crawler\Pipes\Images;
use App\Pipelines\Crawler\Pipes\InternalLinks;
use App\Pipelines\Crawler\Pipes\Title;
use App\Pipelines\Crawler\Pipes\Words;
use DiDom\Document;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CrawlerPipelineTest extends TestCase
{
    use DatabaseMigrations;

    protected Crawl $crawl;
    protected CrawlPage $detail;

    public function setUp(): void
    {
        parent::setUp();

        $this->crawl = Crawl::create([
            'url' => 'https://test_domain.com',
            'pages' => 1,
            'status' => CrawlStatus::RUNNING,
        ]);

        $this->detail = new CrawlPage([
            'crawl_id' => $this->crawl->id,
            'url' => 'https://test_domain.com/page1.html'
        ]);
    }

    /** @test */
    public function title_is_parsed_and_length_is_correct(): void
    {
        $this->detail->document = new Document(file_get_contents('tests/Fixtures/simple_valid_response.html'));

        $next = function (CrawlPage $crawlResult) {
            $this->assertEquals(10, $crawlResult->title_length);
        };

        app()->make(Title::class)->handle($this->detail, $next);
    }

    /** @test */
    public function internal_links_are_parsed_and_counted_correctly(): void
    {
        $this->detail->document = new Document(file_get_contents('tests/Fixtures/simple_valid_response.html'));

        $next = function (CrawlPage $crawlResult) {
            $this->assertEquals(3, $crawlResult->unique_internal_links);
        };

        app()->make(InternalLinks::class)->handle($this->detail, $next);
    }

    /** @test */
    public function external_links_are_parsed_and_counted_correctly(): void
    {
        $this->detail->document = new Document(file_get_contents('tests/Fixtures/simple_valid_response.html'));

        $next = function (CrawlPage $crawlResult) {
            $this->assertEquals(1, $crawlResult->unique_external_links);
        };

        app()->make(ExternalLinks::class)->handle($this->detail, $next);
    }

    /** @test */
    public function images_are_parsed_and_counted_correctly(): void
    {
        $this->detail->document = new Document(file_get_contents('tests/Fixtures/simple_valid_response.html'));

        $next = function (CrawlPage $crawlResult) {
            $this->assertEquals(1, $crawlResult->unique_images);
        };

        app()->make(Images::class)->handle($this->detail, $next);
    }

    /** @test */
    public function words_are_parsed_and_counted_correctly(): void
    {
        $this->detail->document = new Document(file_get_contents('tests/Fixtures/simple_valid_response.html'));

        $next = function (CrawlPage $crawlResult) {
            $this->assertEquals(20, $crawlResult->word_count);
        };

        app()->make(Words::class)->handle($this->detail, $next);
    }
}
