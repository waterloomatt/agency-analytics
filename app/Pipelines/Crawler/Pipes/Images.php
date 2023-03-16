<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Pipelines\Crawler\CrawlResult;
use Closure;

class Images
{
    public const CSS_SELECTOR = 'img';

    public function handle(CrawlResult $result, Closure $next)
    {
        $elements = $result->document->find(self::CSS_SELECTOR);

        $uniqueImageCount = collect($elements)
            ->map(function ($element) {
                return $element->attr('src');
            })
            ->unique()
            ->count();

        $result->imageCount = $uniqueImageCount;

        return $next($result);
    }
}
