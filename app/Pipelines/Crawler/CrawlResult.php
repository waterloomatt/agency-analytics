<?php

namespace App\Pipelines\Crawler;

use App\DTO\Link;
use DiDom\Document;

class CrawlResult
{
    public Document $document;

    public int $httpCode;

    public string $url;

    public float $time;

    public string $title;

    public array $internalLinks = [];

    public function addInternalLink(string $link)
    {
        if (!in_array($link, $this->internalLinks)) {
            $this->internalLinks[] = $link;
        }
    }
}
