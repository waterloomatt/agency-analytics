<?php

namespace App\Console\Commands;

use App\Enums\CrawlStatus;
use App\Models\Crawl;
use App\Models\CrawlDetail;
use App\Pipelines\Crawler\CrawlResult;
use App\Pipelines\Crawler\Pipes\InternalLinks;
use App\Pipelines\Crawler\Pipes\Title;
use DiDom\Document;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;

class Crawler extends Command
{
    public const MAX_VISITS = 6;

    public const DOMAIN_PREFIX = 'https://agencyanalytics.com';
    protected $signature = 'app:crawl {url=https://agencyanalytics.com/}';

    protected $description = 'Crawls a given site and stores the results.';

    protected array $visited = [];
    protected array $toVisit = [];

    protected Crawl $crawl;

    public function handle(): void
    {
        // @todo remove these
        DB::table('crawl_details')->delete();
        DB::table('crawls')->delete();

        $url = $this->argument('url');

        $this->info("Starting to crawl: {$url}");

        $this->crawl = Crawl::create(['status' => CrawlStatus::RUNNING]);

        try {
            $this->crawl($url);

            $this->summarize();
        } catch (Exception $e) {
            $this->crawl->update(['status' => CrawlStatus::ERROR]);
            $this->error($e->getMessage());
        }
    }

    protected function crawl(string $url)
    {
        $result = new CrawlResult();

        $result->url = $url;

        $client = new Client();
        $res = $client->request('GET', $url, [
            'on_stats' => function (TransferStats $stats) use ($result) {
                $result->time = $stats->getTransferTime();
                $result->httpCode = $stats->getResponse()->getStatusCode();
            }
        ]);

        $result->document = new Document($res->getBody()->getContents());

        $this->visited[] = $url;

        app(Pipeline::class)
            ->send($result)
            ->through([
                Title::class,
                InternalLinks::class,
            ])
            ->then(function (CrawlResult $result) {
                CrawlDetail::create([
                    'crawl_id' => $this->crawl->id,
                    'http_status' => $result->httpCode,
                    'url' => $result->url,
                    'page_load' => $result->time,
                    'unique_images' => 0,
                    'unique_internal_links' => count($result->internalLinks),
                    'unique_external_links' => 0,
                    'word_count' => 0,
                    'title_length' => strlen($result->title),
                ]);

                foreach ($result->internalLinks as $link) {
                    $this->toVisit[] = $link;

                    if (!in_array($link, $this->visited) && count($this->visited) < self::MAX_VISITS) {
                        $this->info("Crawling... {$link}");
                        $this->crawl($link);
                    }
                }
            });
    }

    protected function summarize()
    {
        $details = $this->crawl->details;

        $this->crawl->update([
            'status' => CrawlStatus::COMPLETED,
            'avg_page_load' => $details->avg('page_load'),
            'avg_word_count' => $details->avg('word_count'),
            'avg_title_length' => $details->avg('title_length'),
        ]);
    }
}
