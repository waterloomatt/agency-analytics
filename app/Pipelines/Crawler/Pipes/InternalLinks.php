<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Models\CrawlDetail;
use Closure;
use Illuminate\Support\Str;

class InternalLinks
{
    public function handle(CrawlDetail $crawlDetail, Closure $next)
    {
        $parts = parse_url($crawlDetail->url);
        $internalSelectors = sprintf('a[href*=%s], a[href^=/], a[href^=./], a[href^=../], a[href^=#]', $parts['host']);
        $internalLinks = $crawlDetail->document->find($internalSelectors);

        $links = collect($internalLinks)
            ->map(function ($element) use ($crawlDetail) {
                $href = $element->attr('href');

                if (Str::startsWith($href, ['/', '#'])) {
                    $parts = parse_url($crawlDetail->url);
                    $domain = $parts['scheme'] . '://' . $parts['host'];

                    $href = $domain . $href;
                }

                return $href;
            })
            ->filter(fn($href) => trim($href) !== '')
            ->unique()
            ->toArray();

        $crawlDetail->internalLinks = $links;
        $crawlDetail->unique_internal_links = count($links);

        return $next($crawlDetail);
    }
}
