<?php

namespace App\Http\Controllers;

use App\Enums\CrawlStatus;
use App\Http\Requests\CrawlRequest;
use App\Models\Crawl;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CrawlerController extends Controller
{
    public function index()
    {
        $recentCrawls = Crawl::with('details')
            ->recent()
            ->get();

        return view('crawler', [
            'crawls' => $recentCrawls,
        ]);
    }

    public function crawl(CrawlRequest $request)
    {
        $validated = $request->validated();

        try {
            $crawl = Crawl::create([
                'status' => CrawlStatus::RUNNING,
                'url' => $validated['url'],
                'pages' => $validated['pages']
            ]);

            Artisan::call('app:crawl', ['--crawl' => $crawl->id]);
        } catch (Exception $e) {
            Log::error(vsprintf('Error while submitting crawl to %s: %s', [
                $validated['url'],
                $e->getMessage(),
            ]));

            return redirect('/')
                ->withErrors(['exception' => 'We\'re unable to crawl the site at this time. Please try again later.'])
                ->withInput();
        }

        return redirect('/')->withInput();
    }
}
