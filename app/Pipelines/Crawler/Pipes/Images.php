<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Models\CrawlPage;
use Closure;

class Images
{
    public function handle(CrawlPage $crawlPage, Closure $next)
    {
        $elements = $crawlPage->document->find('img');

        $uniqueImageCount = collect($elements)
            ->map(function ($element) {
                return $element->attr('src');
            })
            ->unique()
            ->count();

        $crawlPage->unique_images = $uniqueImageCount;

        return $next($crawlPage);
    }
}
