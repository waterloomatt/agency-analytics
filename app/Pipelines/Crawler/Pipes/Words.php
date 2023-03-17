<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Models\CrawlDetail;
use Closure;
use DiDom\Query;
use Illuminate\Support\Str;

class Words
{
    public function handle(CrawlDetail $crawlDetail, Closure $next)
    {
        $elements = $crawlDetail->document->find('//p | //span | //div | //title//text()/..', Query::TYPE_XPATH);

        $fullText = collect($elements)
            ->map(function ($element) {
                return $element->text();
            })
            ->join(' ');

        $crawlDetail->word_count = Str::wordCount($fullText);

        return $next($crawlDetail);
    }
}
