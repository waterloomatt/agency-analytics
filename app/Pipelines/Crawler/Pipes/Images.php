<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Models\CrawlDetail;
use Closure;

class Images
{
    public function handle(CrawlDetail $crawlDetail, Closure $next)
    {
        $elements = $crawlDetail->document->find('img');

        $uniqueImageCount = collect($elements)
            ->map(function ($element) {
                return $element->attr('src');
            })
            ->unique()
            ->count();

        $crawlDetail->unique_images = $uniqueImageCount;

        return $next($crawlDetail);
    }
}
