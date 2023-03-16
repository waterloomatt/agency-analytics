<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Console\Commands\Crawler;
use App\Pipelines\Crawler\CrawlResult;
use Closure;
use Illuminate\Support\Str;

class NavigableLinks
{
    public function handle(CrawlResult $result, Closure $next)
    {
        $selector = sprintf('a[href*=%s], a[href^=/]', $result->getHost());
        $internalLinks = $result->document->find($selector);

        $links = collect($internalLinks)
            ->map(function ($element) use ($result) {
                $href = $element->attr('href');

                if (Str::startsWith($href, '/')) {
                    $href = $result->buildUrlHost() . $href;
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
