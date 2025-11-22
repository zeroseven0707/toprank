<?php

namespace App\Jobs;

use App\Models\UrlCrawl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StartCrawlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;

    public function __construct(public $crawlId) {}

    public function handle(): void
    {
        $config = UrlCrawl::findOrFail($this->crawlId);

        $payload = [
            'hashtags' => [$config->tag],
            'keywordSearch' => false,
            'resultsLimit' => 5,
            'resultsType' => 'posts',
        ];

        $response = Http::withToken(env('APIFY_TOKEN'))
            ->post(env('APIFY_URL')."/acts/{$config->actor_id}/runs");

        if ($response->failed()) {
            Log::error("❌ Gagal start crawl", $response->json());
            $config->update(['status' => 'failed']);
            return;
        }

        $runId = $response['data']['id'];
        $config->update([
            'run_id' => $runId,
            'status' => 'running'
        ]);

        // lanjut ke pengecekan status, tanpa sleep
        CheckCrawlStatusJob::dispatch($config->id)->delay(now()->addSeconds(30));
    }
}
