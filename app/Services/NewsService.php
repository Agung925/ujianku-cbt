<?php

namespace App\Services;

use App\Models\BeritaCache;
use GuzzleHttp\Client;
use SimpleXMLElement;
use Carbon\Carbon;

class NewsService
{
    protected Client $client;
    protected array $keywords = ['pendidikan', 'kurikulum', 'MTs', 'ujian', 'siswa'];

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10,
            'connect_timeout' => 10,
        ]);
    }

    /**
     * Fetch education news from Google News RSS
     *
     * @param array $keywords
     * @param int $limit
     * @return array
     */
    public function fetchEducationNews(array $keywords = [], int $limit = 10): array
    {
        try {
            $keywords = !empty($keywords) ? $keywords : $this->keywords;
            $searchQuery = implode('+', $keywords);
            $rssUrl = "https://news.google.com/rss/search?q={$searchQuery}";

            $response = $this->client->get($rssUrl);
            $feedContent = (string)$response->getBody();

            return $this->parseRssFeed($feedContent, $limit);
        } catch (\Exception $e) {
            \Log::error('[NewsService] Error fetching education news', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Parse RSS feed and extract news items
     *
     * @param string $feedContent
     * @param int $limit
     * @return array
     */
    protected function parseRssFeed(string $feedContent, int $limit = 10): array
    {
        try {
            $xml = simplexml_load_string($feedContent);
            $news = [];

            if ($xml && isset($xml->channel->item)) {
                $items = (array)$xml->channel->item;
                $itemArray = is_array($items) ? $items : [$items];

                $count = 0;
                foreach ($itemArray as $item) {
                    if ($count >= $limit) break;

                    $title = (string)($item->title ?? 'No Title');
                    $description = (string)($item->description ?? '');
                    $link = (string)($item->link ?? '');
                    $pubDate = (string)($item->pubDate ?? now());
                    $source = (string)($item->source->attributes()->url ?? 'Google News');

                    // Clean description of HTML tags
                    $description = strip_tags($description);
                    $description = htmlspecialchars_decode($description);
                    $description = substr($description, 0, 300);

                    $news[] = [
                        'title' => $title,
                        'description' => $description,
                        'link' => $link,
                        'source' => $source,
                        'published_at' => $this->parseDate($pubDate),
                        'tenant_id' => tenancy()->tenant?->id,
                    ];

                    $count++;
                }
            }

            return $news;
        } catch (\Exception $e) {
            \Log::error('[NewsService] Error parsing RSS feed', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Parse publication date from various formats
     *
     * @param string $dateString
     * @return Carbon
     */
    protected function parseDate(string $dateString): Carbon
    {
        try {
            return Carbon::parse($dateString);
        } catch (\Exception $e) {
            return now();
        }
    }

    /**
     * Get cached news, fetch if expired
     *
     * @param int $limit
     * @param int $expireInHours
     * @return array
     */
    public function getCachedNews(int $limit = 10, int $expireInHours = 1): array
    {
        try {
            $tenantId = tenancy()->tenant?->id;

            // Get non-expired cached news
            $cachedNews = BeritaCache::where('tenant_id', $tenantId)
                ->where('expires_at', '>', now())
                ->orderBy('published_at', 'desc')
                ->limit($limit)
                ->get(['title', 'description', 'link', 'source', 'published_at'])
                ->toArray();

            // If no cached news, fetch new
            if (empty($cachedNews)) {
                $this->fetchAndCache($expireInHours);
                $cachedNews = BeritaCache::where('tenant_id', $tenantId)
                    ->where('expires_at', '>', now())
                    ->orderBy('published_at', 'desc')
                    ->limit($limit)
                    ->get(['title', 'description', 'link', 'source', 'published_at'])
                    ->toArray();
            }

            return $cachedNews;
        } catch (\Exception $e) {
            \Log::error('[NewsService] Error getting cached news', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Fetch and cache news
     *
     * @param int $expireInHours
     * @return int Count of cached items
     */
    public function fetchAndCache(int $expireInHours = 1): int
    {
        try {
            $news = $this->fetchEducationNews($this->keywords, 20);
            $tenantId = tenancy()->tenant?->id;
            $expiresAt = now()->addHours($expireInHours);

            $count = 0;
            foreach ($news as $item) {
                // Check if already exists
                $exists = BeritaCache::where('tenant_id', $tenantId)
                    ->where('title', $item['title'])
                    ->where('source', $item['source'])
                    ->where('published_at', $item['published_at'])
                    ->exists();

                if (!$exists) {
                    BeritaCache::create([
                        'tenant_id' => $tenantId,
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'link' => $item['link'],
                        'source' => $item['source'],
                        'published_at' => $item['published_at'],
                        'expires_at' => $expiresAt,
                    ]);
                    $count++;
                }
            }

            // Delete expired news
            BeritaCache::where('expires_at', '<', now())->delete();

            return $count;
        } catch (\Exception $e) {
            \Log::error('[NewsService] Error fetching and caching news', [
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Get news for display in component
     *
     * @param int $limit
     * @return array
     */
    public function getNewsForDisplay(int $limit = 5): array
    {
        $news = $this->getCachedNews($limit);

        return array_map(function ($item) {
            return [
                'title' => $item['title'],
                'description' => Str::limit($item['description'], 150),
                'link' => $item['link'],
                'source' => $item['source'],
                'date' => Carbon::parse($item['published_at'])->diffForHumans(),
            ];
        }, $news);
    }

    /**
     * Delete expired news older than X days
     *
     * @param int $daysOld
     * @return int
     */
    public function deleteExpiredNews(int $daysOld = 7): int
    {
        try {
            $deleted = BeritaCache::where('created_at', '<', now()->subDays($daysOld))
                ->delete();

            \Log::info('[NewsService] Deleted expired news', ['count' => $deleted]);

            return $deleted;
        } catch (\Exception $e) {
            \Log::error('[NewsService] Error deleting expired news', [
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }
}
