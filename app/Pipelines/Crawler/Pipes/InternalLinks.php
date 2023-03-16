<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Console\Commands\Crawler;
use App\Pipelines\Crawler\CrawlResult;
use Closure;
use Illuminate\Support\Str;

class InternalLinks
{
    public const CSS_SELECTOR = 'a[href*=agencyanalytics.com], a[href^=/], a[href^=#]';

    public function handle(CrawlResult $result, Closure $next)
    {
        $internalLinks = $result->document->find(self::CSS_SELECTOR);

        $linkCount = collect($internalLinks)
            ->map(function ($element) {
                $href = $element->attr('href');

                if (Str::startsWith($href, '/')) {
                    $href = Crawler::DOMAIN_PREFIX . $href;
                }

                return $href;
            })
            ->unique()
            ->count();

        $result->internalLinkCount = $linkCount;

        return $next($result);
    }
}
