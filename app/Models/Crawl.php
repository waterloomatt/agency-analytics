<?php

namespace App\Models;

use App\Enums\CrawlStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crawl extends Model
{
    use HasFactory;

    protected $guarded  = [];
    protected $casts = [
        'status' => CrawlStatus::class,
    ];

    public function details(): HasMany
    {
        return $this->hasMany(CrawlDetail::class);
    }
}
