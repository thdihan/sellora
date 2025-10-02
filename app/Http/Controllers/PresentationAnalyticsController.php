<?php

namespace App\Http\Controllers;

use App\Models\Presentation;
use App\Models\PresentationView;
use App\Models\PresentationDownload;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

/**
 * PresentationAnalyticsController
 *
 * Handles analytics and tracking for presentations
 */
class PresentationAnalyticsController extends Controller
{
    // Authorization is handled in individual methods

    /**
     * Show analytics dashboard for a presentation
     *
     * @param Presentation $presentation
     * @return View
     */
    public function show(Presentation $presentation): View
    {
        // Check if user can view analytics
        if ($presentation->user_id !== Auth::id()) {
            abort(403, 'You do not have permission to view analytics for this presentation.');
        }

        $analytics = $this->getAnalyticsData($presentation);
        
        return view('presentations.analytics', compact('presentation', 'analytics'));
    }

    /**
     * Get analytics data for API
     *
     * @param Presentation $presentation
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Presentation $presentation, Request $request): JsonResponse
    {
        // Check if user can view analytics
        if ($presentation->user_id !== Auth::id()) {
            abort(403, 'You do not have permission to view analytics for this presentation.');
        }

        $period = $request->get('period', '30'); // days
        $analytics = $this->getAnalyticsData($presentation, (int)$period);
        
        return response()->json($analytics);
    }

    /**
     * Get comprehensive analytics data
     *
     * @param Presentation $presentation
     * @param int $days
     * @return array
     */
    private function getAnalyticsData(Presentation $presentation, int $days = 30): array
    {
        $startDate = Carbon::now()->subDays($days);
        
        return [
            'overview' => $this->getOverviewStats($presentation),
            'views' => $this->getViewsAnalytics($presentation, $startDate),
            'downloads' => $this->getDownloadsAnalytics($presentation, $startDate),
            'geographic' => $this->getGeographicData($presentation, $startDate),
            'devices' => $this->getDeviceData($presentation, $startDate),
            'timeline' => $this->getTimelineData($presentation, $startDate),
            'engagement' => $this->getEngagementMetrics($presentation, $startDate)
        ];
    }

    /**
     * Get overview statistics
     *
     * @param Presentation $presentation
     * @return array
     */
    private function getOverviewStats(Presentation $presentation): array
    {
        return [
            'total_views' => $presentation->views_count,
            'total_downloads' => $presentation->downloads_count,
            'unique_viewers' => $presentation->views()->distinct('user_id')->count('user_id'),
            'unique_downloaders' => $presentation->downloads()->distinct('user_id')->count('user_id'),
            'comments_count' => $presentation->comments()->count(),
            'shares_count' => $presentation->shares()->count(),
            'created_at' => $presentation->created_at->format('Y-m-d H:i:s'),
            'last_viewed' => $presentation->views()->latest()->first()?->created_at?->format('Y-m-d H:i:s'),
            'last_downloaded' => $presentation->downloads()->latest()->first()?->created_at?->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Get views analytics
     *
     * @param Presentation $presentation
     * @param Carbon $startDate
     * @return array
     */
    private function getViewsAnalytics(Presentation $presentation, Carbon $startDate): array
    {
        $views = $presentation->views()
            ->where('created_at', '>=', $startDate)
            ->get();

        $dailyViews = $views->groupBy(function ($view) {
            return $view->created_at->format('Y-m-d');
        })->map->count();

        $hourlyViews = $views->groupBy(function ($view) {
            return $view->created_at->format('H');
        })->map->count();

        return [
            'total' => $views->count(),
            'unique' => $views->unique('user_id')->count(),
            'daily' => $dailyViews,
            'hourly' => $hourlyViews,
            'peak_hour' => $hourlyViews->keys()->first() ?? null,
            'average_daily' => round($dailyViews->avg(), 2)
        ];
    }

    /**
     * Get downloads analytics
     *
     * @param Presentation $presentation
     * @param Carbon $startDate
     * @return array
     */
    private function getDownloadsAnalytics(Presentation $presentation, Carbon $startDate): array
    {
        $downloads = $presentation->downloads()
            ->where('created_at', '>=', $startDate)
            ->get();

        $dailyDownloads = $downloads->groupBy(function ($download) {
            return $download->created_at->format('Y-m-d');
        })->map->count();

        return [
            'total' => $downloads->count(),
            'unique' => $downloads->unique('user_id')->count(),
            'daily' => $dailyDownloads,
            'conversion_rate' => $presentation->views_count > 0 
                ? round(($presentation->downloads_count / $presentation->views_count) * 100, 2)
                : 0
        ];
    }

    /**
     * Get geographic data
     *
     * @param Presentation $presentation
     * @param Carbon $startDate
     * @return array
     */
    private function getGeographicData(Presentation $presentation, Carbon $startDate): array
    {
        // This is a simplified implementation
        // In a real application, you would use IP geolocation services
        $views = $presentation->views()
            ->where('created_at', '>=', $startDate)
            ->select('ip_address', DB::raw('count(*) as count'))
            ->groupBy('ip_address')
            ->get();

        return [
            'countries' => [], // Would be populated with actual geolocation data
            'cities' => [],
            'unique_ips' => $views->count()
        ];
    }

    /**
     * Get device/browser data
     *
     * @param Presentation $presentation
     * @param Carbon $startDate
     * @return array
     */
    private function getDeviceData(Presentation $presentation, Carbon $startDate): array
    {
        $views = $presentation->views()
            ->where('created_at', '>=', $startDate)
            ->get();

        $userAgents = $views->pluck('user_agent')->filter();
        
        // Simple user agent parsing (in production, use a proper library)
        $devices = $userAgents->map(function ($ua) {
            if (stripos($ua, 'mobile') !== false || stripos($ua, 'android') !== false) {
                return 'Mobile';
            } elseif (stripos($ua, 'tablet') !== false || stripos($ua, 'ipad') !== false) {
                return 'Tablet';
            }
            return 'Desktop';
        })->countBy();

        $browsers = $userAgents->map(function ($ua) {
            if (stripos($ua, 'chrome') !== false) return 'Chrome';
            if (stripos($ua, 'firefox') !== false) return 'Firefox';
            if (stripos($ua, 'safari') !== false) return 'Safari';
            if (stripos($ua, 'edge') !== false) return 'Edge';
            return 'Other';
        })->countBy();

        return [
            'devices' => $devices,
            'browsers' => $browsers
        ];
    }

    /**
     * Get timeline data
     *
     * @param Presentation $presentation
     * @param Carbon $startDate
     * @return array
     */
    private function getTimelineData(Presentation $presentation, Carbon $startDate): array
    {
        $period = Carbon::now()->diffInDays($startDate);
        
        if ($period <= 7) {
            $format = 'Y-m-d H:00';
            $groupBy = 'hour';
        } elseif ($period <= 30) {
            $format = 'Y-m-d';
            $groupBy = 'day';
        } else {
            $format = 'Y-W';
            $groupBy = 'week';
        }

        $views = $presentation->views()
            ->where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(function ($view) use ($format) {
                return $view->created_at->format($format);
            })
            ->map->count();

        $downloads = $presentation->downloads()
            ->where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(function ($download) use ($format) {
                return $download->created_at->format($format);
            })
            ->map->count();

        return [
            'views' => $views,
            'downloads' => $downloads,
            'period' => $groupBy
        ];
    }

    /**
     * Get engagement metrics
     *
     * @param Presentation $presentation
     * @param Carbon $startDate
     * @return array
     */
    private function getEngagementMetrics(Presentation $presentation, Carbon $startDate): array
    {
        $views = $presentation->views()->where('created_at', '>=', $startDate)->count();
        $downloads = $presentation->downloads()->where('created_at', '>=', $startDate)->count();
        $comments = $presentation->comments()->where('created_at', '>=', $startDate)->count();
        $shares = $presentation->shares()->where('created_at', '>=', $startDate)->count();

        return [
            'engagement_score' => $this->calculateEngagementScore($views, $downloads, $comments, $shares),
            'download_rate' => $views > 0 ? round(($downloads / $views) * 100, 2) : 0,
            'comment_rate' => $views > 0 ? round(($comments / $views) * 100, 2) : 0,
            'share_rate' => $views > 0 ? round(($shares / $views) * 100, 2) : 0
        ];
    }

    /**
     * Calculate engagement score
     *
     * @param int $views
     * @param int $downloads
     * @param int $comments
     * @param int $shares
     * @return float
     */
    private function calculateEngagementScore(int $views, int $downloads, int $comments, int $shares): float
    {
        if ($views === 0) return 0;
        
        // Weighted engagement score
        $score = (
            ($downloads * 3) +  // Downloads are worth 3 points
            ($comments * 2) +   // Comments are worth 2 points
            ($shares * 4) +     // Shares are worth 4 points
            ($views * 1)        // Views are worth 1 point
        ) / $views;
        
        return round($score, 2);
    }
}