<?php

namespace App\Pipelines\Crawler\Pipes;

use App\Models\CrawlPage;
use Closure;
use DiDom\Query;
use Illuminate\Support\Str;

class Words
{
    public function handle(CrawlPage $crawlPage, Closure $next)
    {
        $elements = $crawlPage->document->find('//p | //span | //div//text()/..', Query::TYPE_XPATH);

        $fullText = collect($elements)
            ->map(function ($element) {
                return $element->text();
            })
            ->join(' ');

        $crawlPage->word_count = Str::wordCount($fullText);
        
        return $next($crawlPage);
    }
}
