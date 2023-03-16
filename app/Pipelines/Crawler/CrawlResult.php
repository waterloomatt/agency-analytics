<?php

namespace App\Pipelines\Crawler;

use DiDom\Document;

class CrawlResult
{
    public Document $document;

    public int $httpCode;

    public string $url;

    public float $time;

    public string $title;

    public int $internalLinkCount = 0;

    public int $externalLinkCount = 0;

    public int $imageCount = 0;

    public int $wordCount = 0;

    public array $navigableLinks = [];

    public function buildUrlHost(): ?string
    {
        if (!$this->url) {
            return null;
        }

        $parts = parse_url($this->url);

        return $parts['scheme'] . '://' . $parts['host'];
    }

    public function getHost(): ?string
    {
        if (!$this->url) {
            return null;
        }

        $parts = parse_url($this->url);

        return $parts['host'] ?? null;
    }
}
