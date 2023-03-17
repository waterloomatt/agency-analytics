<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Models\CrawlDetail;
use Closure;

class Title
{
    public function handle(CrawlDetail $crawlDetail, Closure $next)
    {
        $title = '';
        if ($crawlDetail->document->has('head title')) {
            $element = $crawlDetail->document->first('head title');
            $title = $element->text();
        }

        $crawlDetail->title_length = strlen($title);

        return $next($crawlDetail);
    }
}
