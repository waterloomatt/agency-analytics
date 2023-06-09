<?php

namespace App\Console\Commands;

use App\Enums\CrawlStatus;
use App\Models\Crawl;
use App\Models\CrawlPage;
use App\Pipelines\Crawler\Pipes\ExternalLinks;
use App\Pipelines\Crawler\Pipes\Images;
use App\Pipelines\Crawler\Pipes\InternalLinks;
use App\Pipelines\Crawler\Pipes\Title;
use App\Pipelines\Crawler\Pipes\Words;
use DiDom\Document;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\TransferStats;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Log;

class Crawler extends Command
{
    protected $signature = 'app:crawl 
        {--url=https://agencyanalytics.com}
        {--pages=5}';

    protected $description = 'Crawls a given site and stores the results.';

    protected array $visited = [];

    protected Client $client;

    protected ?Crawl $crawl = null;

    public function handle(Client $client): void
    {
        try {
            $this->client = $client;
            $this->crawl = Crawl::create([
                'status' => CrawlStatus::RUNNING,
                'url' => $this->option('url'),
                'page_count' => $this->option('pages'),
            ]);

            $this->info("Starting to crawl");

            $this->crawl($this->crawl->url);

            $this->summarize(CrawlStatus::COMPLETED);

            $this->info("Finished crawling");
        } catch (ClientException $e) {
            $this->logError("Error while retrieving {$this->crawl->url}", $e->getMessage());
            $this->summarize(CrawlStatus::ERROR);
        } catch (Exception $e) {
            $this->logError("Error while crawling {$this->crawl->url}", $e->getMessage());
            $this->summarize(CrawlStatus::ERROR);
        }
    }

    protected function crawl(string $url): void
    {
        $crawlPage = new CrawlPage([
            'crawl_id' => $this->crawl->id,
            'url' => $url,
        ]);

        $this->loadDocument($crawlPage);

        $this->info("Crawling $crawlPage->url");

        app(Pipeline::class)
            ->send($crawlPage)
            ->through([
                Title::class,
                InternalLinks::class,
                ExternalLinks::class,
                Images::class,
                Words::class,
            ])
            ->then(function (CrawlPage $crawlPage) {
                $this->visited[] = $crawlPage->url;

                $crawlPage->save();

                foreach ($crawlPage->internalLinks as $nextUrl) {
                    if ($this->validateNextUrl($nextUrl)) {
                        $this->crawl($nextUrl);
                    }
                }
            });
    }

    protected function validateNextUrl(string $nextUrl): bool
    {
        if (!filter_var($nextUrl, FILTER_VALIDATE_URL)) {
            return false;
        }

        $parts = parse_url($nextUrl);
        if (!array_key_exists('host', $parts)) {
            return false;
        }

        if (!in_array($nextUrl, $this->visited) && count($this->visited) < $this->crawl->page_count) {
            return true;
        }

        return false;
    }

    protected function loadDocument(CrawlPage $crawlPage)
    {
        $response = $this->client->request('GET', $crawlPage->url, [
            'http_errors' => false,
            'timeout' => 5,
            'on_stats' => function (TransferStats $stats) use ($crawlPage) {
                $crawlPage->page_load = $stats->getTransferTime();

                if ($stats->hasResponse()) {
                    $crawlPage->http_status = $stats->getResponse()->getStatusCode();
                }
            }
        ]);

        $crawlPage->document = new Document((string)$response->getBody());
    }

    protected function summarize(CrawlStatus $status): void
    {
        $pages = $this->crawl->pages;

        $summaryData = collect([
            'status' => $status,
            'page_count' => $pages->count(),
        ]);

        if ($pages->count() > 0) {
            $summaryData = $summaryData->merge([
                'unique_images' => $pages->sum('unique_images'),
                'unique_internal_links' => $pages->sum('unique_internal_links'),
                'unique_external_links' => $pages->sum('unique_external_links'),
                'avg_page_load' => $pages->avg('page_load'),
                'avg_word_count' => round($pages->avg('word_count')),
                'avg_title_length' => round($pages->avg('title_length')),
            ]);
        }

        $this->crawl->update($summaryData->toArray());
    }

    protected function logError(string $message, string $exceptionMessage): void
    {
        Log::error($message . ': ' . $exceptionMessage);
        $this->error($message);
    }
}
