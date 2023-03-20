<?php

namespace App\Models;

use App\Enums\CrawlStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Crawl extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' => CrawlStatus::class,
    ];

    public function pages(): HasMany
    {
        return $this->hasMany(CrawlPage::class);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->latest()->limit(5);
    }
}
