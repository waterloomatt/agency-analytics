<?php

namespace App\Console\Commands;

use App\Enums\CrawlStatus;
use App\Models\Crawl;
use App\Models\CrawlDetail;
use App\Pipelines\Crawler\CrawlResult;
use App\Pipelines\Crawler\Pipes\ExternalLinks;
use App\Pipelines\Crawler\Pipes\Images;
use App\Pipelines\Crawler\Pipes\InternalLinks;
use App\Pipelines\Crawler\Pipes\NavigableLinks;
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
        {--crawl=}
        {--url=https://agencyanalytics.com/}
        {--pages=5}';

    protected $description = 'Crawls a given site and stores the results.';

    protected array $visited = [];

    protected array $toVisit = [];

    protected Crawl $crawl;

    public function handle(): void
    {
        $this->crawl = Crawl::firstOrCreate(
            ['id' => $this->option('crawl')],
            [
                'status' => CrawlStatus::RUNNING,
                'url' => $this->option('url'),
                'pages' => $this->option('pages'),
            ]
        );

        $this->info("Starting to crawl: {$this->crawl->url}");

        try {
            $this->crawl($this->crawl->url);
            $this->summarize(CrawlStatus::COMPLETED);
        } catch (ClientException $e) {
            $this->summarize(CrawlStatus::ERROR);
            Log::error('Error while retrieving URL: ' . $e->getMessage());
        } catch (Exception $e) {
            $this->summarize(CrawlStatus::ERROR);
            Log::error('Error while retrieving URL: ' . $e->getMessage());
            $this->error($e->getMessage());
        }
    }

    protected function crawl(string $url)
    {
        $result = new CrawlResult();

        $result->url = $url;

        $client = new Client();
        $res = $client->request('GET', $url, [
            'http_errors' => false,
            'on_stats' => function (TransferStats $stats) use ($result) {
                $result->time = $stats->getTransferTime();

                if ($stats->getResponse()) {
                    $result->httpCode = $stats->getResponse()->getStatusCode();
                }
            }
        ]);

        $result->document = new Document($res->getBody()->getContents());

        $this->visited[] = $url;

        app(Pipeline::class)
            ->send($result)
            ->through([
                Title::class,
                InternalLinks::class,
                ExternalLinks::class,
                NavigableLinks::class,
                Images::class,
                Words::class,
            ])
            ->then(function (CrawlResult $result) {
                $this->storeDetail($result);

                foreach ($result->navigableLinks as $nextUrl) {
                    $this->toVisit[] = $nextUrl;

                    if (!in_array($nextUrl, $this->visited) && count($this->visited) < $this->crawl->pages) {
                        $this->info("Crawling... {$nextUrl}");
                        $this->crawl($nextUrl);
                    }
                }
            });
    }

    protected function storeDetail(CrawlResult $result)
    {
        CrawlDetail::create([
            'crawl_id' => $this->crawl->id,
            'http_status' => $result->httpCode,
            'url' => $result->url,
            'page_load' => $result->time,
            'unique_images' => $result->imageCount,
            'unique_internal_links' => $result->internalLinkCount,
            'unique_external_links' => $result->externalLinkCount,
            'word_count' => $result->wordCount,
            'title_length' => strlen($result->title),
        ]);
    }

    protected function summarize(CrawlStatus $status)
    {
        $details = $this->crawl->details;

        $summaryData = collect([
            'status' => $status,
            'pages' => $details->count(),
        ]);

        if ($details->count() > 0) {
            $summaryData = $summaryData->merge([
                'unique_images' => $details->sum('unique_images'),
                'unique_internal_links' => $details->sum('unique_internal_links'),
                'unique_external_links' => $details->sum('unique_external_links'),
                'avg_page_load' => $details->avg('page_load'),
                'avg_word_count' => round($details->avg('word_count')),
                'avg_title_length' => round($details->avg('title_length')),
            ]);
        }

        $this->crawl->update($summaryData->toArray());
    }
}
