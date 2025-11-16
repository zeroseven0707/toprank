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

class RunCrawlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $urlCrawlId;

    /**
     * Create a new job instance.
     */
    public function __construct($urlCrawlId)
    {
        $this->urlCrawlId = $urlCrawlId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $config = UrlCrawl::find($this->urlCrawlId);
        if (!$config) {
            Log::warning("UrlCrawl ID {$this->urlCrawlId} not found.");
            return;
        }

        $config->update(['status' => 'running']);
        $period = now()->format('Y-m');

        try {
            $payload = [
                'hashtags' => [$config->tag],
                'keywordSearch' => false,
                'resultsLimit' => 5,
                'resultsType' => 'posts',
            ];

            $response = Http::withToken(env('APIFY_TOKEN'))
                ->timeout(120)
                ->retry(3, 5000)
                ->post(env('APIFY_URL') . "/acts/{$config->actor_id}/runs?token=" . env('APIFY_TOKEN'), $payload);

            if ($response->failed()) {
                $config->update(['status' => 'failed']);
                Log::error('Apify run failed', ['response' => $response->json()]);
                return;
            }

            $run = $response->json();
            $datasetId = $run['data']['defaultDatasetId'] ?? null;

            if (!$datasetId) {
                $config->update(['status' => 'failed']);
                Log::error('Dataset ID not found', ['response' => $run]);
                return;
            }

            // tunggu proses Apify
            sleep(30);

            $datasetResponse = Http::withToken(env('APIFY_TOKEN'))->get(env('APIFY_URL') . "/datasets/{$datasetId}/items?token=" . env('APIFY_TOKEN'));

            if ($datasetResponse->failed()) {
                $config->update(['status' => 'failed']);
                Log::error('Dataset fetch failed', ['response' => $datasetResponse->json()]);
                return;
            }

            $items = $datasetResponse->json();

            foreach ($items as $key => $item) {
                $crawl = CrawlData::updateOrCreate(
                    [
                        'url' => $item['url'] ?? null,
                        'period' => $period,
                    ],
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
                    ],
                );

                Content::updateOrCreate(
                    [
                        'url' => $crawl->url,
                        'period' => $period,
                    ],
                    [
                        'name' => '',
                        'rank' => $key + 1,
                        'category_content_id' => $config->category,
                        'city' => $config->city,
                        'description' => '',
                        'url' => $crawl->url,
                        'crawl_data_id' => $crawl->id,
                    ],
                );
            }

            $config->update([
                'status' => 'done',
                'last_run_at' => now(),
            ]);

            Log::info("✅ Crawl sukses untuk {$config->name}");
        } catch (\Throwable $e) {
            $config->update(['status' => 'failed']);
            Log::error('Crawl job gagal', [
                'url_crawl_id' => $this->urlCrawlId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
