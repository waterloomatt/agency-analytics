<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Console\Commands\Crawler;
use App\Pipelines\Crawler\CrawlResult;
use Closure;
use Illuminate\Support\Str;

class NavigableLinks
{
    public const CSS_SELECTOR = 'a[href*=agencyanalytics.com], a[href^=/]';

    public function handle(CrawlResult $result, Closure $next)
    {
        $internalLinks = $result->document->find(self::CSS_SELECTOR);

        $links = collect($internalLinks)
            ->map(function ($element) {
                $href = $element->attr('href');

                if (Str::startsWith($href, '/')) {
                    $href = Crawler::DOMAIN_PREFIX . $href;
                }

                return $href;
            })
            ->filter(function ($link) {
                return !Str::startsWith($link, '#');
            })
            ->unique()
            ->toArray();

        $result->navigableLinks = $links;

        return $next($result);
    }
}
