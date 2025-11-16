<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Apify\CrawlController;

class RunAllCrawlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-all-crawl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalankan semua URL crawl otomatis setiap tanggal 1';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new CrawlController();
        $controller->runAll();

        $this->info('✅ Semua URL crawl berhasil dijalankan melalui scheduler.');
    }
}
