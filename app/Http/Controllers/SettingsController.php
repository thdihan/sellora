<?php

/**
 * Settings Controller for managing application settings
 *
 * This file contains the SettingsController class which handles
 * comprehensive application settings across multiple categories.
 *
 * PHP version 8.1
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   WebNexa <info@webnexa.eporichoy.com>
 * @license  MIT License
 * @link     https://www.webnexa.eporichoy.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use ZipArchive;
use Carbon\Carbon;

/**
 * Settings Controller
 *
 * Handles comprehensive application settings across multiple categories:
 * Profile, Company, App, Email/Notifications, Security, Backup/Integrations, Updates
 * Admin-only access control for sensitive settings.
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   WebNexa <info@webnexa.eporichoy.com>
 * @license  MIT License
 * @link     https://www.webnexa.eporichoy.com
 */
class SettingsController extends Controller
{
    /**
     * Constructor - Apply admin middleware to sensitive routes
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user() || !Auth::user()->role || !in_array(Auth::user()->role->name, ['Author', 'Admin', 'NSM+'])) {
                abort(403, 'Access denied. Admin privileges required.');
            }
            return $next($request);
        })->except(['index', 'profile', 'updateProfile']);
    }
    /**
     * Display the main settings dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $isAdmin = Auth::user() && Auth::user()->role && in_array(Auth::user()->role->name, ['Admin', 'NSM+']);
        
        return view('settings.index', compact('isAdmin'));
    }
    
    /**
     * Display profile settings (accessible to all users)
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        return view('settings.profile');
    }
    
    /**
     * Update profile settings
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'designation' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'date_of_joining' => 'nullable|date',
            'blood_group' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'timezone' => 'nullable|string',
            'address' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'marketing_emails' => 'boolean',
            'security_alerts' => 'boolean',
        ]);
        
        $user = auth()->user();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'designation' => $request->designation,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'date_of_joining' => $request->date_of_joining,
            'blood_group' => $request->blood_group,
            'timezone' => $request->timezone,
            'address' => $request->address,
            'bio' => $request->bio,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
            'email_notifications' => $request->has('email_notifications'),
            'sms_notifications' => $request->has('sms_notifications'),
            'marketing_emails' => $request->has('marketing_emails'),
            'security_alerts' => $request->has('security_alerts'),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!'
        ]);
    }
    
    /**
     * Upload profile photo
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $user = auth()->user();
        
        // Delete old photo if exists
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }
        
        // Store new photo
        $path = $request->file('photo')->store('profile-photos', 'public');
        
        $user->update(['photo' => $path]);
        
        return response()->json([
            'success' => true,
            'message' => 'Profile picture uploaded successfully!',
            'photo_url' => asset('storage/' . $path)
        ]);
    }
    
    /**
     * Remove profile photo
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function removePhoto()
    {
        $user = auth()->user();
        
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }
        
        $user->update(['photo' => null]);
        
        return response()->json([
            'success' => true,
            'message' => 'Profile picture removed successfully!'
        ]);
    }
    
    /**
     * Update password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);
        
        $user = auth()->user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ]);
        }
        
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully!'
        ]);
    }
    
    /**
     * Display company settings
     *
     * @return \Illuminate\View\View
     */
    public function company()
    {
        $settings = [
            'company_name' => Setting::get('company_name', ''),
            'company_address' => Setting::get('company_address', ''),
            'company_phone' => Setting::get('company_phone', ''),
            'company_email' => Setting::get('company_email', ''),
            'company_website' => Setting::get('company_website', ''),
            'company_logo' => Setting::get('company_logo', ''),
            'footer_brand_html' => Setting::get('footer_brand_html', ''),
        ];
        
        return view('settings.company', compact('settings'));
    }
    
    /**
     * Update company settings
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCompany(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'nullable|string|max:500',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'company_website' => 'nullable|url|max:255',
            'footer_brand_html' => 'nullable|string|max:1000',
        ]);
        
        foreach ($request->only(['company_name', 'company_address', 'company_phone', 'company_email', 'company_website', 'footer_brand_html']) as $key => $value) {
            Setting::set($key, $value);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Company settings updated successfully.'
        ]);
    }
    
    /**
     * Display application settings
     *
     * @return \Illuminate\View\View
     */
    public function app()
    {
        $settings = [
            'app_name' => Setting::get('app_name', config('app.name')),
            'app_debug' => Setting::get('app_debug', false),
            'app_maintenance' => Setting::get('app_maintenance', false),
            'session_timeout' => Setting::get('session_timeout', 120),
            'max_file_upload' => Setting::get('max_file_upload', 10),
            'allowed_file_types' => Setting::get('allowed_file_types', 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx'),
        ];
        
        return view('settings.app', compact('settings'));
    }
    
    /**
     * Update application settings
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateApp(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_debug' => 'boolean',
            'app_maintenance' => 'boolean',
            'session_timeout' => 'required|integer|min:5|max:1440',
            'max_file_upload' => 'required|integer|min:1|max:100',
            'allowed_file_types' => 'required|string|max:500',
        ]);
        
        foreach ($request->only(['app_name', 'app_debug', 'app_maintenance', 'session_timeout', 'max_file_upload', 'allowed_file_types']) as $key => $value) {
            Setting::set($key, $value);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Application settings updated successfully.'
        ]);
    }
    
    /**
     * Display email and notification settings
     *
     * @return \Illuminate\View\View
     */
    public function email()
    {
        $settings = [
            'mail_driver' => Setting::get('mail_driver', 'smtp'),
            'mail_host' => Setting::get('mail_host', ''),
            'mail_port' => Setting::get('mail_port', 587),
            'mail_username' => Setting::get('mail_username', ''),
            'mail_password' => Setting::get('mail_password', ''),
            'mail_encryption' => Setting::get('mail_encryption', 'tls'),
            'mail_from_address' => Setting::get('mail_from_address', ''),
            'mail_from_name' => Setting::get('mail_from_name', ''),
            'notifications_enabled' => Setting::get('notifications_enabled', true),
            'email_notifications' => Setting::get('email_notifications', true),
        ];
        
        return view('settings.email', compact('settings'));
    }
    
    /**
     * Update email and notification settings
     *
     * @param  Request $request The HTTP request object
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'mail_driver' => 'required|in:smtp,sendmail,mailgun,ses',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
            'notifications_enabled' => 'boolean',
            'email_notifications' => 'boolean',
        ]);
        
        foreach ($request->only(['mail_driver', 'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name', 'notifications_enabled', 'email_notifications']) as $key => $value) {
            Setting::set($key, $value);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Email settings updated successfully.'
        ]);
    }
    
    /**
     * Display security settings
     *
     * @return \Illuminate\View\View
     */
    public function security()
    {
        $settings = [
            'password_min_length' => Setting::get('password_min_length', 8),
            'password_require_uppercase' => Setting::get('password_require_uppercase', true),
            'password_require_lowercase' => Setting::get('password_require_lowercase', true),
            'password_require_numbers' => Setting::get('password_require_numbers', true),
            'password_require_symbols' => Setting::get('password_require_symbols', false),
            'login_attempts_limit' => Setting::get('login_attempts_limit', 5),
            'login_lockout_duration' => Setting::get('login_lockout_duration', 15),
            'two_factor_enabled' => Setting::get('two_factor_enabled', false),
            'session_secure_cookies' => Setting::get('session_secure_cookies', true),
        ];
        
        return view('settings.security', compact('settings'));
    }
    
    /**
     * Update security settings
     *
     * @param  Request $request The HTTP request object
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSecurity(Request $request)
    {
        $request->validate([
            'password_min_length' => 'required|integer|min:6|max:50',
            'password_require_uppercase' => 'boolean',
            'password_require_lowercase' => 'boolean',
            'password_require_numbers' => 'boolean',
            'password_require_symbols' => 'boolean',
            'login_attempts_limit' => 'required|integer|min:3|max:20',
            'login_lockout_duration' => 'required|integer|min:5|max:1440',
            'two_factor_enabled' => 'boolean',
            'session_secure_cookies' => 'boolean',
        ]);
        
        foreach ($request->only(['password_min_length', 'password_require_uppercase', 'password_require_lowercase', 'password_require_numbers', 'password_require_symbols', 'login_attempts_limit', 'login_lockout_duration', 'two_factor_enabled', 'session_secure_cookies']) as $key => $value) {
            Setting::set($key, $value);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Security settings updated successfully.'
        ]);
    }
    
    /**
     * Display backup and integration settings
     *
     * @return \Illuminate\View\View
     */
    public function backup()
    {
        $settings = [
            'backup_enabled' => Setting::get('backup_enabled', false),
            'backup_frequency' => Setting::get('backup_frequency', 'daily'),
            'backup_retention_days' => Setting::get('backup_retention_days', 30),
            'backup_storage_path' => Setting::get('backup_storage_path', 'backups'),
            'api_integrations_enabled' => Setting::get('api_integrations_enabled', false),
            'webhook_url' => Setting::get('webhook_url', ''),
            'external_api_key' => Setting::get('external_api_key', ''),
        ];
        
        return view('settings.backup', compact('settings'));
    }
    
    /**
     * Update backup and integration settings
     *
     * @param  Request $request The HTTP request object
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBackup(Request $request)
    {
        $request->validate([
            'backup_enabled' => 'boolean',
            'backup_frequency' => 'required|in:hourly,daily,weekly,monthly',
            'backup_retention_days' => 'required|integer|min:1|max:365',
            'backup_storage_path' => 'required|string|max:255',
            'api_integrations_enabled' => 'boolean',
            'webhook_url' => 'nullable|url|max:500',
            'external_api_key' => 'nullable|string|max:255',
        ]);
        
        foreach ($request->only(['backup_enabled', 'backup_frequency', 'backup_retention_days', 'backup_storage_path', 'api_integrations_enabled', 'webhook_url', 'external_api_key']) as $key => $value) {
            Setting::set($key, $value);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Backup and integration settings updated successfully.'
        ]);
    }
    
    /**
     * Display application update settings
     *
     * @return \Illuminate\View\View
     */
    public function updates()
    {
        $currentVersion = Setting::get('app_version', '1.0.0');
        $updateHistory = Setting::get('update_history', []);
        
        return view('settings.updates', compact('currentVersion', 'updateHistory'));
    }
    
    /**
     * Handle application update via ZIP upload
     *
     * @param  Request $request The HTTP request object
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadUpdate(Request $request)
    {
        $request->validate([
            'update_file' => 'required|file|mimes:zip|max:51200', // 50MB max
            'version' => 'required|string|max:20',
            'description' => 'nullable|string|max:500',
        ]);
        
        try {
            $file = $request->file('update_file');
            $version = $request->version;
            $description = $request->description ?? 'Application update';
            
            // Create backup of current version
            $this->_createBackup();
            
            // Store uploaded file
            $updatePath = 'updates/' . $version;
            $filePath = $file->storeAs($updatePath, 'update.zip', 'local');
            
            // Extract and apply update
            $extractPath = storage_path('app/' . $updatePath . '/extracted');
            $this->_extractUpdate(storage_path('app/' . $filePath), $extractPath);
            
            // Log update
            $this->_logUpdate($version, $description);
            
            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully to version ' . $version
            ]);
            
        } catch (\Exception $e) {
            Log::error('Update failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Rollback to previous version
     *
     * @param  Request $request The HTTP request object
     * @return \Illuminate\Http\JsonResponse
     */
    public function rollback(Request $request)
    {
        $request->validate([
            'version' => 'required|string|max:20',
        ]);
        
        try {
            $version = $request->version;
            $backupPath = storage_path('app/backups/' . $version);
            
            if (!file_exists($backupPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup for version ' . $version . ' not found.'
                ], 404);
            }
            
            // Restore from backup
            $this->_restoreBackup($backupPath);
            
            // Update version
            Setting::set('app_version', $version);
            
            // Log rollback
            $this->_logUpdate($version, 'Rollback to version ' . $version);
            
            return response()->json([
                'success' => true,
                'message' => 'Successfully rolled back to version ' . $version
            ]);
            
        } catch (\Exception $e) {
            Log::error('Rollback failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Rollback failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update update settings
     *
     * @param  Request $request The HTTP request object
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUpdateSettings(Request $request)
    {
        try {
            $settings = [
                'auto_backup' => $request->boolean('auto_backup'),
                'update_notifications' => $request->boolean('update_notifications'),
                'rollback_enabled' => $request->boolean('rollback_enabled'),
                'version_logging' => $request->boolean('version_logging'),
                'max_rollback_versions' => $request->input('max_rollback_versions', 5),
                'update_timeout' => $request->input('update_timeout', 30),
            ];

            foreach ($settings as $key => $value) {
                Setting::set($key, $value);
            }

            return response()->json([
                'success' => true,
                'message' => 'Update settings saved successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save settings: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Clear application cache
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * View update logs
     *
     * @return \Illuminate\Http\Response
     */
    public function viewLogs()
    {
        $logPath = storage_path('logs/updates.log');
        
        if (!file_exists($logPath)) {
            return response('No update logs found.', 404);
        }
        
        $logs = file_get_contents($logPath);
        
        return response($logs, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'inline; filename="updates.log"'
        ]);
    }
    
    /**
     * Update footer brand settings
     *
     * @param Request $request The HTTP request object
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFooterBrand(Request $request)
    {
        $isLocked = Setting::get('footer_brand_locked', 'false') === 'true';
        
        // Check if user has permission to modify locked settings
        if ($isLocked && (!Auth::user() || !Auth::user()->role || Auth::user()->role->name !== 'Author')) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'You do not have permission to modify this setting.'
                ],
                403
            );
        }
        
        $request->validate(
            [
                'footer_brand_html' => 'required|string|max:1000'
            ]
        );
        
        Setting::set('footer_brand_html', $request->footer_brand_html);
        
        return response()->json(
            [
                'success' => true,
                'message' => 'Footer brand updated successfully.'
            ]
        );
    }
    
    /**
     * Toggle footer brand lock (Author only)
     *
     * @param Request $request The HTTP request object
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleFooterBrandLock(Request $request)
    {
        if (!Auth::user() || !Auth::user()->role || Auth::user()->role->name !== 'Author') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Only Authors can modify lock settings.'
                ],
                403
            );
        }
        
        $currentLock = Setting::get('footer_brand_locked', 'false');
        $newLock = $currentLock === 'true' ? 'false' : 'true';
        
        Setting::set('footer_brand_locked', $newLock);
        
        $message = 'Footer brand lock ' . ($newLock === 'true' ? 'enabled' : 'disabled') . ' successfully.';
        
        return response()->json(
            [
                'success' => true,
                'message' => $message,
                'locked' => $newLock === 'true'
            ]
        );
    }
    
    /**
     * Create backup of current application
     *
     * @return void
     */
    private function _createBackup()
    {
        $currentVersion = Setting::get('app_version', '1.0.0');
        $backupPath = storage_path('app/backups/' . $currentVersion);
        
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }
        
        // Create ZIP backup of application files
        $zip = new ZipArchive();
        $zipFile = $backupPath . '/backup.zip';
        
        if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
            $this->_addDirectoryToZip(base_path(), $zip, 'app/');
            $zip->close();
        }
    }
    
    /**
     * Extract update ZIP file
     *
     * @param  string $zipPath    Path to ZIP file
     * @param  string $extractPath Path to extract to
     * @return void
     */
    private function _extractUpdate($zipPath, $extractPath)
    {
        $zip = new ZipArchive();
        
        if ($zip->open($zipPath) === TRUE) {
            if (!file_exists($extractPath)) {
                mkdir($extractPath, 0755, true);
            }
            
            $zip->extractTo($extractPath);
            $zip->close();
            
            // Copy extracted files to application directory
            $this->_copyDirectory($extractPath, base_path());
        }
    }
    
    /**
     * Restore from backup
     *
     * @param  string $backupPath Path to backup directory
     * @return void
     */
    private function _restoreBackup($backupPath)
    {
        $zipFile = $backupPath . '/backup.zip';
        
        if (file_exists($zipFile)) {
            $zip = new ZipArchive();
            
            if ($zip->open($zipFile) === TRUE) {
                $zip->extractTo(base_path());
                $zip->close();
            }
        }
    }
    
    /**
     * Log update information
     *
     * @param  string $version     Version number
     * @param  string $description Update description
     * @return void
     */
    private function _logUpdate($version, $description)
    {
        $updateHistory = Setting::get('update_history', []);
        
        $updateHistory[] = [
            'version' => $version,
            'description' => $description,
            'date' => Carbon::now()->toDateTimeString(),
            'user' => Auth::user()->name ?? 'System'
        ];
        
        Setting::set('update_history', $updateHistory);
        Setting::set('app_version', $version);
        
        Log::info('Application updated', [
            'version' => $version,
            'description' => $description,
            'user' => Auth::user()->name ?? 'System'
        ]);
    }
    
    /**
     * Add directory to ZIP archive
     *
     * @param  string     $dir    Directory path
     * @param  ZipArchive $zip    ZIP archive object
     * @param  string     $prefix Path prefix
     * @return void
     */
    private function _addDirectoryToZip($dir, $zip, $prefix = '')
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $prefix . substr($filePath, strlen($dir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
    
    /**
     * Copy directory recursively
     *
     * @param  string $src  Source directory
     * @param  string $dest Destination directory
     * @return void
     */
    private function _copyDirectory($src, $dest)
    {
        $dir = opendir($src);
        
        if (!file_exists($dest)) {
            mkdir($dest, 0755, true);
        }
        
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($src . '/' . $file)) {
                    $this->_copyDirectory($src . '/' . $file, $dest . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dest . '/' . $file);
                }
            }
        }
        
        closedir($dir);
    }
    
    /**
     * Show theme settings page
     *
     * @return \Illuminate\View\View
     */
    public function theme()
    {
        $themeSettings = [
            'primary_color' => Setting::get('theme_primary_color', '#007bff'),
            'secondary_color' => Setting::get('theme_secondary_color', '#6c757d'),
            'dark_mode' => Setting::get('theme_dark_mode', false),
            'sidebar_style' => Setting::get('theme_sidebar_style', 'default'),
            'font_family' => Setting::get('theme_font_family', 'Inter'),
            'font_size' => Setting::get('theme_font_size', '14px'),
        ];
        
        return view('settings.theme', compact('themeSettings'));
    }
    
    /**
     * Update theme settings
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTheme(Request $request)
    {
        $request->validate([
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'dark_mode' => 'boolean',
            'sidebar_style' => 'required|string|in:default,compact,mini',
            'font_family' => 'required|string|max:50',
            'font_size' => 'required|string|max:10',
        ]);
        
        // TODO: Implement theme settings update logic
        Setting::set('theme_primary_color', $request->primary_color);
        Setting::set('theme_secondary_color', $request->secondary_color);
        Setting::set('theme_dark_mode', $request->boolean('dark_mode'));
        Setting::set('theme_sidebar_style', $request->sidebar_style);
        Setting::set('theme_font_family', $request->font_family);
        Setting::set('theme_font_size', $request->font_size);
        
        return redirect()->route('settings.theme')
            ->with('success', 'Theme settings updated successfully!');
    }
    
    /**
     * Show email test page
     *
     * @return \Illuminate\View\View
     */
    public function emailTest()
    {
        $emailSettings = [
            'smtp_host' => Setting::get('smtp_host'),
            'smtp_port' => Setting::get('smtp_port'),
            'smtp_username' => Setting::get('smtp_username'),
            'smtp_encryption' => Setting::get('smtp_encryption'),
            'mail_from_address' => Setting::get('mail_from_address'),
            'mail_from_name' => Setting::get('mail_from_name'),
        ];
        
        return view('settings.email.test', compact('emailSettings'));
    }
    
    /**
     * Send test email
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
            'test_subject' => 'required|string|max:255',
            'test_message' => 'required|string',
        ]);
        
        try {
            // TODO: Implement actual email sending logic
            \Illuminate\Support\Facades\Mail::raw(
                $request->test_message,
                function ($message) use ($request) {
                    $message->to($request->test_email)
                        ->subject($request->test_subject);
                }
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $request->test_email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display VAT & TAX settings
     *
     * @return \Illuminate\View\View
     */
    public function tax()
    {
        $settings = [
            'vat_rate' => Setting::get('vat_rate', 15),
            'tax_rate' => Setting::get('tax_rate', 5),
        ];
        
        return view('settings.tax', compact('settings'));
    }

    /**
     * Update VAT & TAX settings
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTax(Request $request)
    {
        $request->validate([
            'vat_rate' => 'required|numeric|min:0|max:100',
            'tax_rate' => 'required|numeric|min:0|max:100',
        ]);
        
        Setting::set('vat_rate', $request->vat_rate);
        Setting::set('tax_rate', $request->tax_rate);
        
        return response()->json([
            'success' => true,
            'message' => 'VAT & TAX settings updated successfully.'
        ]);
    }

    /**
     * Get tax rates for API.
     */
    public function getTaxRates()
    {
        $vatRate = Setting::get('vat_rate', 15);
        $taxRate = Setting::get('tax_rate', 5);

        return response()->json([
            'vat_rate' => (float) $vatRate,
            'tax_rate' => (float) $taxRate,
        ]);
    }
}
