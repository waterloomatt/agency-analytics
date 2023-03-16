<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Console\Commands\Crawler;
use App\Pipelines\Crawler\CrawlResult;
use Closure;
use Illuminate\Support\Str;

class ExternalLinks
{
    public function handle(CrawlResult $result, Closure $next)
    {
        $allLinks = collect($result->document->find('a'));
        $internalLinks = $result->document->find(InternalLinks::CSS_SELECTOR);
        $externalLinks = $allLinks->diff($internalLinks);

        $linkCount = collect($externalLinks)
            ->map(function ($element) {
                $href = $element->attr('href');

                if (Str::startsWith($href, '/')) {
                    $href = Crawler::DOMAIN_PREFIX . $href;
                }

                return $href;
            })
            ->unique()
            ->count();

        $result->externalLinkCount = $linkCount;

        return $next($result);
    }
}
