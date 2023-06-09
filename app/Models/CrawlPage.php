<?php

namespace App\Models;

use DiDom\Document;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrawlPage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public Document $document;
    public array $internalLinks = [];

    public function crawl(): BelongsTo
    {
        return $this->belongsTo(Crawl::class);
    }
}
