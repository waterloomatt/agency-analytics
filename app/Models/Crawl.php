<?php

namespace App\Models;

use App\Enums\CrawlStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crawl extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => CrawlStatus::class,
    ];
}
