<?php

namespace App\Http\Controllers;

use App\Enums\CrawlStatus;
use App\Http\Requests\CrawlRequest;
use App\Models\Crawl;
use Illuminate\Support\Facades\Artisan;

class CrawlerController extends Controller
{
    public function index()
    {
        $crawls = Crawl::with('details')
            ->latest()
            ->limit(5)
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

        Artisan::call('app:crawl', ['--crawl' => $crawl->id]);

        return redirect('/')->withInput();
    }
}