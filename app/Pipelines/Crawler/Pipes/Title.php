<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Pipelines\Crawler\CrawlResult;
use Closure;

class Title
{
    public function handle(CrawlResult $result, Closure $next)
    {
        $result->title = $result->document->first('head title');

        return $next($result);
    }
}