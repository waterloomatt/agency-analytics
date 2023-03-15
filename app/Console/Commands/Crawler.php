<?php

namespace App\Console\Commands;

use App\Enums\CrawlStatus;
use App\Models\Crawl;
use App\Pipelines\Crawler\CrawlResult;
use App\Pipelines\Crawler\Pipes\InternalLinks;
use App\Pipelines\Crawler\Pipes\Title;
use DiDom\Document;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;

class Crawler extends Command
{
    public const MAX_VISITS = 6;

    public const DOMAIN_PREFIX = 'https://agencyanalytics.com';
    protected $signature = 'app:crawl {url=https://agencyanalytics.com/}';

    protected $description = 'Crawls a given site and stores the results.';

    protected array $visited = [];
    protected array $toVisit = [];

    public function handle(): void
    {
        $url = $this->argument('url');

        $this->info("Starting to crawl: {$url}");

        $this->crawl($url);
    }

    protected function crawl(string $url)
    {
        $result = new CrawlResult();

        $client = new Client();
        $res = $client->request('GET', $url, [
            'on_stats' => function (TransferStats $stats) use ($result) {
                $result->time = $stats->getTransferTime();
                $result->httpCode = $stats->getResponse()->getStatusCode();
            }
        ]);

        $result->document = new Document($res->getBody()->getContents());

        $this->visited[] = $url;

        Crawl::create([
            'status' => CrawlStatus::RUNNING
        ]);

        app(Pipeline::class)
            ->send($result)
            ->through([
                Title::class,
                InternalLinks::class,
            ])
            ->then(function (CrawlResult $result) {
                foreach ($result->internalLinks as $link) {
                    $this->toVisit[] = $link;

                    if (!in_array($link, $this->visited) && count($this->visited) < self::MAX_VISITS) {
                        $this->info("Crawling... {$link}");
                        $this->crawl($link);
                    }
                }
            });
    }

    protected function store(CrawlResult $result)
    {

    }
}
