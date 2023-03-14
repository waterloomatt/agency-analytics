<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Crawl extends Command
{
    protected $signature = 'app:crawl {url=https://agencyanalytics.com/}';

    protected $description = 'Crawls a given site and stores the results.';

    public function handle(): void
    {
        $url = $this->argument('url');

        $this->info("Starting to crawl: {$url}");
    }
}
