<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Models\CrawlDetail;
use Closure;
use DiDom\Document;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;

class LoadDocument
{
    public function handle(CrawlDetail $crawlDetail, Closure $next)
    {
        $client = new Client();
        $response = $client->request('GET', $crawlDetail->url, [
            'http_errors' => false,
            'on_stats' => function (TransferStats $stats) use ($crawlDetail) {
                $crawlDetail->page_load = $stats->getTransferTime();

                if ($stats->hasResponse()) {
                    $crawlDetail->http_status = $stats->getResponse()->getStatusCode();
                }
            }
        ]);

        $crawlDetail->document = new Document((string)$response->getBody());

        return $next($crawlDetail);
    }
}
