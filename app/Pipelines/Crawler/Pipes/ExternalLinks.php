<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Models\CrawlPage;
use Closure;

class ExternalLinks
{
    public function handle(CrawlPage $crawlPage, Closure $next)
    {
        $allLinks = collect($crawlPage->document->find('a'));

        $parts = parse_url($crawlPage->url);
        $internalSelectors = sprintf('a[href*=%s], a[href^=/], a[href^=./], a[href^=../], a[href^=#]', $parts['host']);
        $internalLinks = $crawlPage->document->find($internalSelectors);

        $externalLinks = $allLinks->diff($internalLinks);

        $links = collect($externalLinks)
            ->map(fn($element) => $element->attr('href'))
            ->filter(fn($href) => trim($href) !== '')
            ->unique()
            ->toArray();

        $crawlPage->unique_external_links = count($links);

        return $next($crawlPage);
    }
}
