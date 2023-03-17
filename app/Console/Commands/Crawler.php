<?php

namespace App\Console\Commands;

use App\Enums\CrawlStatus;
use App\Models\Crawl;
use App\Models\CrawlDetail;
use App\Pipelines\Crawler\Pipes\ExternalLinks;
use App\Pipelines\Crawler\Pipes\Images;
use App\Pipelines\Crawler\Pipes\InternalLinks;
use App\Pipelines\Crawler\Pipes\LoadDocument;
use App\Pipelines\Crawler\Pipes\Title;
use App\Pipelines\Crawler\Pipes\Words;
use Exception;
use GuzzleHttp\Exception\ClientException;
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

            $this->info("Starting to crawl");

            $this->crawl($this->crawl->url);

            $this->summarize(CrawlStatus::COMPLETED);

            $this->info("Finished crawling");
        } catch (ClientException $e) {
            $this->summarize(CrawlStatus::ERROR);
            $this->logError("Error while retrieving {$this->crawl->url}", $e->getMessage());
        } catch (Exception $e) {
            $this->summarize(CrawlStatus::ERROR);
            $this->logError("Error while crawling {$this->crawl->url}", $e->getMessage());
        }
    }

    protected function crawl(string $url): void
    {
        $crawlDetail = new CrawlDetail([
            'crawl_id' => $this->crawl->id,
            'url' => $url,
        ]);

        $this->info("Crawling $crawlDetail->url");

        app(Pipeline::class)
            ->send($crawlDetail)
            ->through([
                LoadDocument::class,
                Title::class,
                InternalLinks::class,
                ExternalLinks::class,
                Images::class,
                Words::class,
            ])
            ->then(function (CrawlDetail $crawlDetail) {
                $this->visited[] = $crawlDetail->url;

                $crawlDetail->save();

                foreach ($crawlDetail->internalLinks as $nextUrl) {
                    if (!in_array($nextUrl, $this->visited) && count($this->visited) < $this->crawl->pages) {
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

    protected function logError(string $message, string $exceptionMessage): void
    {
        Log::error($message . ': ' . $exceptionMessage);
        $this->error($message);
    }
}
