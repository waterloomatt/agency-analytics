<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Models\CrawlPage;
use Closure;
use Illuminate\Support\Str;

class InternalLinks
{
    public function handle(CrawlPage $crawlPage, Closure $next)
    {
        $parts = parse_url($crawlPage->url);
        $internalSelectors = sprintf('a[href*=%s], a[href^=/], a[href^=./], a[href^=../], a[href^=#]', $parts['host']);
        $internalLinks = $crawlPage->document->find($internalSelectors);

        $links = collect($internalLinks)
            ->map(function ($element) use ($crawlPage) {
                $href = $element->attr('href');

                if (Str::startsWith($href, ['/', '#'])) {
                    $parts = parse_url($crawlPage->url);
                    $domain = $parts['scheme'] . '://' . $parts['host'];

                    $href = $domain . $href;
                }

                return $href;
            })
            ->filter(fn($href) => trim($href) !== '')
            ->unique()
            ->toArray();

        $crawlPage->internalLinks = $links;
        $crawlPage->unique_internal_links = count($links);

        return $next($crawlPage);
    }
}
