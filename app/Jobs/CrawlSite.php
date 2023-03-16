<?php

namespace App\Jobs;

use App\Models\Crawl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class CrawlSite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Crawl $crawl)
    {
    }

    public function handle(): void
    {
        Artisan::call('app:crawl', ['crawl' => $this->crawl->id]);
    }
}
