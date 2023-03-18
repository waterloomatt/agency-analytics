<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Models\CrawlDetail;
use Closure;

class ExternalLinks
{
    public function handle(CrawlDetail $crawlDetail, Closure $next)
    {
        $allLinks = collect($crawlDetail->document->find('a'));

        $parts = parse_url($crawlDetail->url);
        $internalSelectors = sprintf('a[href*=%s], a[href^=/], a[href^=./], a[href^=../], a[href^=#]', $parts['host']);
        $internalLinks = $crawlDetail->document->find($internalSelectors);

        $externalLinks = $allLinks->diff($internalLinks);

        $links = collect($externalLinks)
            ->map(fn($element) => $element->attr('href'))
            ->filter(fn($href) => trim($href) !== '')
            ->unique()
            ->toArray();

        $crawlDetail->unique_external_links = count($links);

        return $next($crawlDetail);
    }
}
