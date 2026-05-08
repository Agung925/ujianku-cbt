<?php

namespace App\Jobs;

use App\Services\NewsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FetchEducationNewsJob implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying a job that encountered an uncaught exception.
     *
     * @var int
     */
    public $backoff = 300;

    /**
     * Execute the job.
     */
    public function handle(NewsService $newsService): void
    {
        try {
            $count = $newsService->fetchAndCache(1);
            Log::info('[FetchEducationNewsJob] Fetched and cached news', ['count' => $count]);
        } catch (\Exception $e) {
            Log::error('[FetchEducationNewsJob] Error fetching news', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
