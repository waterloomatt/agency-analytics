<?php

namespace App\Models;

use DiDom\Document;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrawlDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public Document $document;
    public array $internalLinks = [];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Crawl::class);
    }
}
