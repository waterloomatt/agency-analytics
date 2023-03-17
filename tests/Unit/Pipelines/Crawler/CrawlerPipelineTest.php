<?php

namespace Tests\Unit\Pipelines\Crawler;

use App\Models\CrawlDetail;
use App\Pipelines\Crawler\Pipes\ExternalLinks;
use App\Pipelines\Crawler\Pipes\Images;
use App\Pipelines\Crawler\Pipes\InternalLinks;
use App\Pipelines\Crawler\Pipes\Title;
use App\Pipelines\Crawler\Pipes\Words;
use DiDom\Document;
use Tests\TestCase;

class CrawlerPipelineTest extends TestCase
{
    protected CrawlDetail $detail;

    public function setUp(): void
    {
        parent::setUp();

        $this->detail = new CrawlDetail([
            'crawl_id' => 1,
            'url' => 'https://test_domain.com'
        ]);
    }

    /** @test */
    public function title_is_parsed_and_length_is_correct(): void
    {
        $this->detail->document = new Document(file_get_contents('tests/Fixtures/simple_valid_response.html'));

        $next = function (CrawlDetail $crawlResult) {
            $this->assertEquals(10, $crawlResult->title_length);
        };

        app()->make(Title::class)->handle($this->detail, $next);
    }

    /** @test */
    public function internal_links_are_parsed_and_counted_correctly(): void
    {
        $this->detail->document = new Document(file_get_contents('tests/Fixtures/simple_valid_response.html'));

        $next = function (CrawlDetail $crawlResult) {
            $this->assertEquals(3, $crawlResult->unique_internal_links);
        };

        app()->make(InternalLinks::class)->handle($this->detail, $next);
    }

    /** @test */
    public function external_links_are_parsed_and_counted_correctly(): void
    {
        $this->detail->document = new Document(file_get_contents('tests/Fixtures/simple_valid_response.html'));

        $next = function (CrawlDetail $crawlResult) {
            $this->assertEquals(1, $crawlResult->unique_external_links);
        };

        app()->make(ExternalLinks::class)->handle($this->detail, $next);
    }

    /** @test */
    public function images_are_parsed_and_counted_correctly(): void
    {
        $this->detail->document = new Document(file_get_contents('tests/Fixtures/simple_valid_response.html'));

        $next = function (CrawlDetail $crawlResult) {
            $this->assertEquals(1, $crawlResult->unique_images);
        };

        app()->make(Images::class)->handle($this->detail, $next);
    }

    /** @test */
    public function words_are_parsed_and_counted_correctly(): void
    {
        $this->detail->document = new Document(file_get_contents('tests/Fixtures/simple_valid_response.html'));

        $next = function (CrawlDetail $crawlResult) {
            $this->assertEquals(22, $crawlResult->word_count);
        };

        app()->make(Words::class)->handle($this->detail, $next);
    }
}
