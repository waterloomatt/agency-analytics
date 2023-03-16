<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrawlRequest;
use App\Jobs\CrawlSite;

class CrawlerController extends Controller
{
    public function index()
    {
        return view('crawler', []);
    }

    public function crawl(CrawlRequest $request)
    {
        $url = $request->validated('url');

        CrawlSite::dispatch($url);

        return redirect('/')->with('status', "Crawl started for $url");
    }
}