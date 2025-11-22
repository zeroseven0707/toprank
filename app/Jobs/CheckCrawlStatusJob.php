<?php

namespace App\Jobs;

use App\Models\UrlCrawl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CheckCrawlStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public $crawlId) {}

    public function handle(): void
    {
        $config = UrlCrawl::findOrFail($this->crawlId);

        $run = Http::withToken(env('APIFY_TOKEN'))
            ->get(env('APIFY_URL')."/runs/{$config->run_id}")
            ->json();

        $status = $run['status'] ?? null;

        if ($status !== 'SUCCEEDED') {
            // schedule ulang, tanpa blocking
            return self::dispatch($this->crawlId)->delay(now()->addSeconds(30));
        }

        // Kalau sudah selesai, lanjut fetch data
        SaveCrawlItemsJob::dispatch($this->crawlId, 0);
    }
}
