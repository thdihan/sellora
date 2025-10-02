<?php

/**
 * Notification Controller File
 *
 * This file contains the NotificationController class for managing notifications.
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Notification Controller
 *
 * Handles notification management operations including marking notifications
 * as read, getting unread counts, and managing notification states.
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class NotificationController extends Controller
{
    /**
     * Mark a specific notification as read
     *
     * @param string $id The notification ID
     *
     * @return JsonResponse
     */
    public function markRead($id): JsonResponse
    {
        $notification = auth()->user()->unreadNotifications()->findOrFail($id);
        $notification->markAsRead();
        
        return response()->json(
            [
                'count' => auth()->user()->unreadNotifications()->count()
            ]
        );
    }

    /**
     * Mark all notifications as read
     *
     * @return JsonResponse
     */
    public function markAll(): JsonResponse
    {
        auth()->user()->unreadNotifications->markAsRead();
        
        return response()->json(
            [
                'count' => 0
            ]
        );
    }

    /**
     * Get unread notification count
     *
     * @return JsonResponse
     */
    public function unreadCount(): JsonResponse
    {
        return response()->json(
            [
                'count' => auth()->user()->unreadNotifications()->count()
            ]
        );
    }
}