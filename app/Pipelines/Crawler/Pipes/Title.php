<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Models\CrawlDetail;
use Closure;

class Title
{
    public function handle(CrawlDetail $crawlDetail, Closure $next)
    {
        $title = $crawlDetail->document->first('head title') ?? '';

        $crawlDetail->title_length = strlen($title->text());

        return $next($crawlDetail);
    }
}
