<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Models\CrawlPage;
use Closure;

class Title
{
    public function handle(CrawlPage $crawlPage, Closure $next)
    {
        $title = '';
        if ($crawlPage->document->has('head title')) {
            $title = $crawlPage->document->first('head title')->text();
        }

        $crawlPage->title_length = strlen($title);

        return $next($crawlPage);
    }
}
