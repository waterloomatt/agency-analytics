<?php

namespace App\Http\Controllers;

use App\Enums\CrawlStatus;
use App\Http\Requests\CrawlRequest;
use App\Jobs\CrawlSite;
use App\Models\Crawl;

class CrawlerController extends Controller
{
    public function index()
    {
        $crawls = Crawl::with('details')
            ->latest()
            ->get();

        return view('crawler', [
            'crawls' => $crawls,
        ]);
    }

    public function crawl(CrawlRequest $request)
    {
        $validated = $request->validated();

        $crawl = Crawl::create([
            'status' => CrawlStatus::RUNNING,
            'url' => $validated['url'],
            'pages' => $validated['pages']
        ]);

        CrawlSite::dispatch($crawl);

        return redirect('/')->with('status', "Crawl started for {$validated['url']}");
    }
}