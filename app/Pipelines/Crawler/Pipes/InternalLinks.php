<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Pipelines\Crawler\CrawlResult;
use Closure;
use Illuminate\Support\Str;

class InternalLinks
{
    public function handle(CrawlResult $result, Closure $next)
    {
        $selector = sprintf('a[href*=%s], a[href^=/], a[href^=#]', $result->getHost());
        $internalLinks = $result->document->find($selector);

        $linkCount = collect($internalLinks)
            ->map(function ($element) use ($result) {
                $href = $element->attr('href');

                if (Str::startsWith($href, '/')) {
                    $href = $result->buildUrlHost() . $href;
                }

                return $href;
            })
            ->unique()
            ->count();

        $result->internalLinkCount = $linkCount;

        return $next($result);
    }
}
