<?php

namespace App\Jobs;

use App\Models\UrlCrawl;
use App\Models\CrawlData;
use App\Models\Content;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SaveCrawlItemsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    public function __construct(public $crawlId, public $offset = 0) {}

    public function handle(): void
    {
        $config = UrlCrawl::findOrFail($this->crawlId);

        $limit = 50;
        $period = now()->format('Y-m');

        $dataset = Http::withToken(env('APIFY_TOKEN'))
            ->get(env('APIFY_URL')."/datasets/{$config->run_id}/items", [
                'offset' => $this->offset,
                'limit' => $limit
            ])->json();

        if (empty($dataset)) {
            $config->update([
                'status' => 'done',
                'last_run_at' => now(),
            ]);
            return;
        }

        foreach ($dataset as $key => $item) {
            $crawl = CrawlData::updateOrCreate(
                ['url' => $item['url'] ?? null, 'period' => $period],
                [
                    'url_crawl_id' => $config->id,
                    'city' => $config->city,
                    'category' => $config->category,
                    'caption' => $item['caption'] ?? null,
                    'ownerFullName' => $item['ownerFullName'] ?? null,
                    'ownerUsername' => $item['ownerUsername'] ?? null,
                    'commentsCount' => $item['commentsCount'] ?? 0,
                    'firstComment' => $item['firstComment'] ?? null,
                    'likesCount' => $item['likesCount'] ?? 0,
                    'hashtags' => $item['hashtags'] ?? [],
                    'scraped_at' => now(),
                    'metadata' => $item,
                ]
            );

            Content::updateOrCreate(
                ['url' => $crawl->url, 'period' => $period],
                [
                    'rank' => $this->offset + $key + 1,
                    'category_content_id' => $config->category,
                    'city' => $config->city,
                    'crawl_data_id' => $crawl->id,
                ]
            );
        }

        // lanjut batch berikutnya
        SaveCrawlItemsJob::dispatch($config->id, $this->offset + $limit);
    }
}
