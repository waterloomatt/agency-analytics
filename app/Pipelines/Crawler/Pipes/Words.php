<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Pipelines\Crawler\CrawlResult;
use Closure;
use DiDom\Query;
use Illuminate\Support\Str;

class Words
{
    public const XPATH_SELECTOR = '//p | //span | //div//text()/..';

    public function handle(CrawlResult $result, Closure $next)
    {
        $elements = $result->document->find(self::XPATH_SELECTOR, Query::TYPE_XPATH);

        $fullText = collect($elements)
            ->map(function ($element) {
                return $element->text();
            })
            ->join(' ');

        $result->wordCount = Str::wordCount($fullText);

        return $next($result);
    }
}
