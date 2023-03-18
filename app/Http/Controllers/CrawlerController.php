<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrawlRequest;
use App\Models\Crawl;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CrawlerController extends Controller
{
    public function index()
    {
        try {
            $recentCrawls = Crawl::with('details')
                ->recent()
                ->get();
        } catch (Exception $e) {
            Log::error(sprintf('Error while loading crawl data: %s', $e->getMessage()));
        }

        return view('crawler', [
            'crawls' => $recentCrawls,
            'mostRecentCrawl' => $recentCrawls->first(),
        ]);
    }

    public function crawl(CrawlRequest $request)
    {
        $validated = $request->validated();

        try {
            Artisan::call('app:crawl', [
                '--url' => $validated['url'],
                '--pages' => $validated['pages']
            ]);
        } catch (Exception $e) {
            Log::error(sprintf('Error while submitting crawl to %s: %s', $validated['url'], $e->getMessage()));

            return redirect('/')
                ->withErrors(['exception' => 'We\'re unable to crawl the site at this time. Please try again later.'])
                ->withInput();
        }

        return redirect('/')->withInput();
    }
}
