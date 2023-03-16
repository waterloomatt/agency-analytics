<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Pipelines\Crawler\CrawlResult;
use Closure;

class ExternalLinks
{
    public function handle(CrawlResult $result, Closure $next)
    {
        $allLinks = collect($result->document->find('a'));
        $selector = sprintf('a[href*=%s], a[href^=/], a[href^=#]', $result->getHost());
        $internalLinks = $result->document->find($selector);
        $externalLinks = $allLinks->diff($internalLinks);

        $linkCount = collect($externalLinks)
            ->map(function ($element) {
                $href = $element->attr('href');

                return $href;
            })
            ->unique()
            ->count();

        $result->externalLinkCount = $linkCount;

        return $next($result);
    }
}
