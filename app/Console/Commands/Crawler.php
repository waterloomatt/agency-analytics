<?php

namespace App\Console\Commands;

use App\Enums\CrawlStatus;
use App\Models\Crawl;
use App\Models\CrawlDetail;
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
        {--crawl=}
        {--url=https://agencyanalytics.com/}
        {--pages=5}';

    protected $description = 'Crawls a given site and stores the results.';

    protected array $visited = [];

    protected array $toVisit = [];

    protected Crawl $crawl;

    public function handle(): void
    {
        try {
            $this->crawl = Crawl::firstOrCreate(
                ['id' => $this->option('crawl')],
                [
                    'status' => CrawlStatus::RUNNING,
                    'url' => $this->option('url'),
                    'pages' => $this->option('pages'),
                ]
            );

            $this->info("Starting to crawl: {$this->crawl->url}");

            $this->crawl($this->crawl->url);

            $this->summarize(CrawlStatus::COMPLETED);
        } catch (ClientException $e) {
            $this->summarize(CrawlStatus::ERROR);
            $this->logError($e->getMessage(), "Error while retrieving {$this->crawl->url}");
        } catch (Exception $e) {
            $this->summarize(CrawlStatus::ERROR);
            $this->logError($e->getMessage(), "Error while crawling {$this->crawl->url}");
        }
    }

    protected function crawl(string $url): void
    {
        $crawlDetail = new CrawlDetail([
            'crawl_id' => $this->crawl->id,
            'url' => $url,
        ]);

        $client = new Client();
        $response = $client->request('GET', $url, [
            'http_errors' => false,
            'on_stats' => function (TransferStats $stats) use ($crawlDetail) {
                $crawlDetail->page_load = $stats->getTransferTime();

                if ($stats->hasResponse()) {
                    $crawlDetail->http_status = $stats->getResponse()->getStatusCode();
                }
            }
        ]);

        $crawlDetail->document = new Document((string)$response->getBody());

        app(Pipeline::class)
            ->send($crawlDetail)
            ->through([
                Title::class,
                InternalLinks::class,
                ExternalLinks::class,
                Images::class,
                Words::class,
            ])
            ->then(function (CrawlDetail $crawlDetail) {
                $this->visited[] = $crawlDetail->url;

//                dd($crawlDetail);
                $crawlDetail->save();

                foreach ($crawlDetail->internalLinks as $nextUrl) {
                    $this->toVisit[] = $nextUrl;

                    if (!in_array($nextUrl, $this->visited) && count($this->visited) < $this->crawl->pages) {
                        $this->info("Crawling... $nextUrl");
                        $this->crawl($nextUrl);
                    }
                }
            });
    }

    protected function summarize(CrawlStatus $status): void
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

    protected function logError(string $exceptionMessage, string $message): void
    {
        Log::error($message . ': ' . $exceptionMessage);
        $this->error($message);
    }
}
