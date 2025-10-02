<?php

namespace App\Services;

use App\Models\Presentation;
use App\Models\PresentationView;
use App\Models\PresentationDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * PresentationTrackingService
 *
 * Handles tracking of presentation views and downloads
 */
class PresentationTrackingService
{
    /**
     * Track a presentation view
     *
     * @param Presentation $presentation
     * @param Request $request
     * @return PresentationView
     */
    public function trackView(Presentation $presentation, Request $request): PresentationView
    {
        // Check if this user/IP has already viewed this presentation recently (within 1 hour)
        $recentView = PresentationView::where('presentation_id', $presentation->id)
            ->where(function ($query) use ($request) {
                if (Auth::check()) {
                    $query->where('user_id', Auth::id());
                } else {
                    $query->where('ip_address', $request->ip());
                }
            })
            ->where('created_at', '>=', now()->subHour())
            ->first();

        // If no recent view, create a new one
        if (!$recentView) {
            $view = PresentationView::create([
                'presentation_id' => $presentation->id,
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Increment the presentation views count
            $presentation->increment('views_count');

            return $view;
        }

        return $recentView;
    }

    /**
     * Track a presentation download
     *
     * @param Presentation $presentation
     * @param Request $request
     * @return PresentationDownload
     */
    public function trackDownload(Presentation $presentation, Request $request): PresentationDownload
    {
        $download = PresentationDownload::create([
            'presentation_id' => $presentation->id,
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Increment the presentation downloads count
        $presentation->increment('downloads_count');

        return $download;
    }

    /**
     * Get popular presentations based on views
     *
     * @param int $limit
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPopularPresentations(int $limit = 10, int $days = 30)
    {
        return Presentation::withCount([
            'views' => function ($query) use ($days) {
                $query->where('created_at', '>=', now()->subDays($days));
            }
        ])
        ->orderBy('views_count', 'desc')
        ->limit($limit)
        ->get();
    }

    /**
     * Get trending presentations (high engagement recently)
     *
     * @param int $limit
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTrendingPresentations(int $limit = 10, int $days = 7)
    {
        return Presentation::withCount([
            'views' => function ($query) use ($days) {
                $query->where('created_at', '>=', now()->subDays($days));
            },
            'downloads' => function ($query) use ($days) {
                $query->where('created_at', '>=', now()->subDays($days));
            },
            'comments' => function ($query) use ($days) {
                $query->where('created_at', '>=', now()->subDays($days));
            }
        ])
        ->get()
        ->map(function ($presentation) {
            // Calculate engagement score
            $presentation->engagement_score = 
                ($presentation->views_count * 1) +
                ($presentation->downloads_count * 3) +
                ($presentation->comments_count * 2);
            return $presentation;
        })
        ->sortByDesc('engagement_score')
        ->take($limit);
    }

    /**
     * Get user's presentation viewing history
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserViewHistory(int $userId, int $limit = 20)
    {
        return PresentationView::with('presentation')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get presentation statistics for a user
     *
     * @param int $userId
     * @return array
     */
    public function getUserPresentationStats(int $userId): array
    {
        $presentations = Presentation::where('user_id', $userId)->get();
        
        return [
            'total_presentations' => $presentations->count(),
            'total_views' => $presentations->sum('views_count'),
            'total_downloads' => $presentations->sum('downloads_count'),
            'average_views_per_presentation' => $presentations->count() > 0 
                ? round($presentations->avg('views_count'), 2) 
                : 0,
            'most_viewed_presentation' => $presentations->sortByDesc('views_count')->first(),
            'most_downloaded_presentation' => $presentations->sortByDesc('downloads_count')->first(),
        ];
    }

    /**
     * Clean old tracking data
     *
     * @param int $days
     * @return int Number of deleted records
     */
    public function cleanOldTrackingData(int $days = 365): int
    {
        $cutoffDate = now()->subDays($days);
        
        $deletedViews = PresentationView::where('created_at', '<', $cutoffDate)->delete();
        $deletedDownloads = PresentationDownload::where('created_at', '<', $cutoffDate)->delete();
        
        return $deletedViews + $deletedDownloads;
    }
}