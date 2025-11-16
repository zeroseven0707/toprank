<?php

namespace App\Http\Controllers\Apify;

use App\Http\Controllers\Controller;
use App\Jobs\RunCrawlJob;
use App\Models\Content;
use App\Models\UrlCrawl;
use App\Models\CrawlData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrawlController extends Controller
{
    public function run($id)
    {
        $config = UrlCrawl::findOrFail($id);

        $config->update(['status' => 'queued']);

        try {
            RunCrawlJob::dispatch($id);

            return back()->with('success', "Crawl untuk {$config->name} telah dijadwalkan di background queue 🚀");
        } catch (\Throwable $e) {
            Log::error('Gagal dispatch Crawl Job', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menjadwalkan crawl.');
        }
    }
    public function runAll()
    {
        try {
            $urlCrawls = UrlCrawl::all();

            foreach ($urlCrawls as $crawl) {
                $crawl->update(['status' => 'running']);

                RunCrawlJob::dispatch($crawl->id);
            }

            return back()->with('success', 'Semua URL Crawl sedang dijalankan!');
        } catch (\Throwable $e) {
            \Log::error('Gagal menjalankan semua crawl', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menjalankan semua crawl.');
        }
    }
}
