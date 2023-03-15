<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Console\Commands\Crawler;
use App\Pipelines\Crawler\CrawlResult;
use Closure;
use Illuminate\Support\Str;

class InternalLinks
{
    public function handle(CrawlResult $result, Closure $next)
    {
        $internalLinks = $result->document->find('a[href*=agencyanalytics.com], a[href^=/]');

        foreach ($internalLinks as $link) {
            $href = $link->attr('href');

            if (Str::startsWith($href, '/')) {
                $href = Crawler::DOMAIN_PREFIX . $href;
            }

            $result->addInternalLink($href);
        }

        return $next($result);
    }
}
