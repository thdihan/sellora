<?php

/**
 * Web Routes
 *
 * Here is where you can register web routes for your application. These
 * routes are loaded by the RouteServiceProvider and all of them will
 * be assigned to the "web" middleware group. Make something great!
 *
 * @category Routes
 * @package  Sellora
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\TaxRuleController;
use App\Http\Controllers\ApiConnectorController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\SelfAssessmentController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\SalesTargetController;
use App\Http\Controllers\PresentationController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\PresentationAnalyticsController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductBrandController;
use Illuminate\Support\Facades\Route;

Route::get(
    '/',
    function () {
        return \Illuminate\Support\Facades\Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
    }
);

// Dashboard Route
Route::middleware(['auth'])->group(
    function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Notification routes
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notif.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAll'])->name('notif.readAll');
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    }
);

// Orders Management
Route::middleware(['auth'])->group(
    function () {
        // Orders routes
        Route::resource('orders', OrderController::class);
        Route::patch('/orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
        Route::get(
            '/orders/{order}/attachments/{index}',
            [OrderController::class, 'downloadAttachment']
        )->name('orders.download-attachment');
        
        // Customer Management Routes
        Route::resource('customers', CustomerController::class);
        Route::get('/api/customers/search', [CustomerController::class, 'search'])->name('api.customers.search');
        Route::get(
            '/api/customers/{customer}/summary',
            [CustomerController::class, 'summary']
        )->name('api.customers.summary');
        
        // Product search route for order creation
        Route::get('/api/products/search', [ProductController::class, 'apiSearch'])->name('api.products.search');
        
        // Product stock route for order creation (restricted to Author/Admin)
        Route::get('products/{product}/stock', [ProductController::class, 'getAvailableStock'])->name('products.stock');
        
        // Order-related product routes (accessible to all authenticated users)
        Route::get(
            '/api/order-products/{product}/info',
            [\App\Http\Controllers\OrderProductController::class, 'getProductInfo']
        )->name('api.order-products.info');
        Route::get(
            '/api/order-products',
            [\App\Http\Controllers\OrderProductController::class, 'getActiveProducts']
        )->name('api.order-products.list');
        
        // Expenses Management Routes
        Route::resource('expenses', ExpenseController::class);
        Route::patch('/expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
        Route::patch('/expenses/{expense}/reject', [ExpenseController::class, 'reject'])->name('expenses.reject');
        Route::patch(
            '/expenses/{expense}/mark-paid',
            [ExpenseController::class, 'markAsPaid']
        )->name('expenses.mark-paid');
        Route::get(
            '/expenses/{expense}/attachments/{index}',
            [ExpenseController::class, 'downloadAttachment']
        )->name('expenses.download-attachment');
        
        // Bills Management Routes
        Route::resource('bills', BillController::class);
        Route::patch('/bills/{bill}/approve', [BillController::class, 'approve'])->name('bills.approve');
        Route::patch('/bills/{bill}/reject', [BillController::class, 'reject'])->name('bills.reject');
        Route::patch('/bills/{bill}/mark-paid', [BillController::class, 'markAsPaid'])->name('bills.mark-paid');
        Route::get(
            '/bills/{bill}/attachments/{index}',
            [BillController::class, 'downloadAttachment']
        )->name('bills.download-attachment');
        
        // Visits Management Routes
        Route::resource('visits', VisitController::class);
        Route::get('/visits/calendar', [VisitController::class, 'calendar'])->name('visits.calendar');
        Route::patch('/visits/{visit}/approve', [VisitController::class, 'approve'])->name('visits.approve');
        Route::post('/visits/{visit}/start', [VisitController::class, 'start'])->name('visits.start');
        Route::post('/visits/{visit}/complete', [VisitController::class, 'complete'])->name('visits.complete');
        Route::post('/visits/{visit}/cancel', [VisitController::class, 'cancel'])->name('visits.cancel');
        Route::get(
            '/visits/{visit}/attachments/{index}',
            [VisitController::class, 'downloadAttachment']
        )->name('visits.download-attachment');
        
        // Events Management Routes
        Route::resource('events', EventController::class);
        Route::get('/events/calendar', [EventController::class, 'calendar'])->name('events.calendar');
        Route::get('/events/upcoming', [EventController::class, 'upcoming'])->name('events.upcoming');
        Route::post('/events/{event}/duplicate', [EventController::class, 'duplicate'])->name('events.duplicate');
        Route::get(
            '/events/{event}/attachments/{index}',
            [EventController::class, 'downloadAttachment']
        )->name('events.download-attachment');
    }
);

// Product Management Routes (Author and Admin only)
Route::middleware(['auth', 'role:Author,Admin'])->group(
    function () {
        Route::resource('products', ProductController::class);
        Route::post('/products/bulk-import', [ProductController::class, 'bulkImport'])->name('products.bulk-import');
        Route::delete('/products/{product}/remove-file', [ProductController::class, 'removeFile'])->name('products.remove-file');
        Route::get(
            '/products/{product}/attachments/{index}',
            [ProductController::class, 'downloadAttachment']
        )->name('products.download-attachment');
        Route::get('/products/sync', [ProductController::class, 'syncIndex'])->name('products.sync.index');
        Route::post('/products/sync/process', [ProductController::class, 'processSync'])->name('products.sync.process');
        Route::post('/products/{product}/sync', [ProductController::class, 'processSync'])->name('products.sync');
        
        // Product Import Routes
        Route::get(
            '/products/import',
            function () {
                return view('products.import.index');
            }
        )->name('products.import.index');
        Route::get(
            '/products/import/sql',
            function () {
                return view('products.import.sql');
            }
        )->name('products.import.sql');
        Route::get(
            '/products/import/csv',
            function () {
                return view('products.import.csv');
            }
        )->name('products.import.csv');
        Route::get(
            '/products/import/excel',
            function () {
                return view('products.import.excel');
            }
        )->name('products.import.excel');
        Route::get(
            '/products/import/full-db',
            function () {
                return view('products.import.full-db');
            }
        )->name('products.import.full-db');
        Route::post('/products/import', [ProductController::class, 'import'])->name('products.import.process');
        Route::post('/products/import/csv', [ProductController::class, 'import'])->name('products.import.csv.process');
        Route::post('/products/import/excel', [ProductController::class, 'import'])->name('products.import.excel.process');
        Route::post('/products/import/sql', [ProductController::class, 'import'])->name('products.import.sql.process');
        Route::post('/products/import/full-db', [ProductController::class, 'import'])->name('products.import.full-db.process');
        
        // Product Category Management Routes
        Route::resource('product-categories', ProductCategoryController::class);
        
        // Product Brand Management Routes
        Route::resource('product-brands', ProductBrandController::class);
        
        // Inventory Management Routes
        Route::resource('inventory', InventoryController::class);
        Route::get('/inventory/batches', [InventoryController::class, 'batches'])->name('inventory.batches');
        Route::get('/inventory/transactions', [InventoryController::class, 'transactions'])->name('inventory.transactions');
        Route::get('/inventory/adjustments', [InventoryController::class, 'adjustments'])->name('inventory.adjustments');
        Route::post('/inventory/adjustments', [InventoryController::class, 'storeAdjustment'])->name('inventory.adjustments.store');
        Route::get('/inventory/transfers', [InventoryController::class, 'transfers'])->name('inventory.transfers');
        Route::post('/inventory/transfers', [InventoryController::class, 'storeTransfer'])->name('inventory.transfers.store');
        Route::post('/inventory/bulk-update', [InventoryController::class, 'bulkUpdate'])->name('inventory.bulk-update');
        Route::get('/inventory/export', [InventoryController::class, 'export'])->name('inventory.export');
        Route::post('/inventory/import', [InventoryController::class, 'import'])->name('inventory.import');
        
        // Warehouse Management Routes
        
    }
);

// User Management Routes (Author and Admin)
Route::middleware(['auth', 'role:Author,Admin'])->group(
    function () {
        Route::resource('users', UserController::class);
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/bulk-update-roles', [UserController::class, 'bulkUpdateRoles'])->name('users.bulk-update-roles');
        
        // Location Management Routes
        Route::resource('locations', LocationController::class);
        Route::get('/locations/export', [LocationController::class, 'exportAll'])->name('locations.export');
        Route::get(
            '/team-map',
            function () {
                return view('team-map');
            }
        )->name('location.team-map');
        
        // Tax Management Routes
        Route::resource('taxes', TaxController::class);
        Route::resource('tax-rates', TaxRateController::class);
        Route::resource('tax-rules', TaxRuleController::class);
        
        // API Connector Routes
        Route::resource('api-connectors', ApiConnectorController::class);
        Route::post(
            '/api-connectors/{connector}/test',
            [ApiConnectorController::class, 'test']
        )->name('api-connectors.test');
    }
);

// Assessment Routes
Route::middleware(['auth'])->group(
    function () {
        Route::resource('assessments', AssessmentController::class);
        Route::resource('self-assessments', SelfAssessmentController::class);
        Route::get('/self-assessments/history', [SelfAssessmentController::class, 'history'])->name('self-assessments.history');
        Route::get('/assessments/{assessment}/export', [AssessmentController::class, 'export'])->name('assessments.export');
        Route::patch('/assessments/{assessment}/approve', [AssessmentController::class, 'approve'])->name('assessments.approve');
        Route::patch(
            '/self-assessments/{selfAssessment}/approve',
            [SelfAssessmentController::class, 'approve']
        )->name('self-assessments.approve');
    }
);

// Budget Management Routes
Route::middleware(['auth'])->group(
    function () {
        Route::resource('budgets', BudgetController::class);
        Route::patch('/budgets/{budget}/approve', [BudgetController::class, 'approve'])->name('budgets.approve');
    }
);

// Sales Target Routes
Route::middleware(['auth'])->group(
    function () {
        Route::resource('sales-targets', SalesTargetController::class);
        Route::get('/sales-targets/bulk-create', [SalesTargetController::class, 'bulkCreate'])->name('sales-targets.bulk-create');
        Route::get('/sales-targets/{target}/progress', [SalesTargetController::class, 'progress'])->name('sales-targets.progress');
    }
);

// Presentation Routes
Route::middleware(['auth'])->group(
    function () {
        Route::resource('presentations', PresentationController::class);
        Route::get('/presentations/export', [PresentationController::class, 'export'])->name('presentations.export');
        Route::get('/presentations/auto-reports', [PresentationController::class, 'autoReports'])->name('presentations.auto-reports');
        Route::post(
            '/presentations/generate-from-report/{reportId}',
            [PresentationController::class, 'generateFromReport']
        )->name('presentations.generate-from-report');
        Route::get(
            '/presentations/{presentation}/attachments/{index}',
            [PresentationController::class, 'downloadAttachment']
        )->name('presentations.download-attachment');
    }
);

// Reports Routes
Route::middleware(['auth'])->group(
    function () {
        Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
        Route::get('/reports/sales', [ReportsController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/expenses', [ReportsController::class, 'expenses'])->name('reports.expenses');
        Route::get('/reports/visits', [ReportsController::class, 'visits'])->name('reports.visits');
        Route::get('/reports/budgets', [ReportsController::class, 'budgets'])->name('reports.budgets');
        Route::get('/reports/custom', [ReportsController::class, 'custom'])->name('reports.custom');
        Route::post('/reports/custom', [ReportsController::class, 'generateCustom'])->name('reports.generateCustom');
        Route::get('/reports/export', [ReportsController::class, 'export'])->name('reports.export');
    }
);

// Presentation Analytics Routes
Route::middleware('auth')->prefix('presentations/{presentation}/analytics')->group(
    function () {
        Route::get('/', [PresentationAnalyticsController::class, 'show'])->name('presentations.analytics');
        Route::get('/data', [PresentationAnalyticsController::class, 'data'])->name('presentations.analytics.data');
    }
);

Route::middleware('auth')->group(
    function () {
        // Settings Routes
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
        
        // Settings Sub-routes
        Route::get('/settings/profile', [SettingsController::class, 'profile'])->name('settings.profile');
        Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
        Route::post('/settings/profile/upload-photo', [SettingsController::class, 'uploadPhoto'])->name('settings.profile.upload-photo');
        Route::delete('/settings/profile/remove-photo', [SettingsController::class, 'removePhoto'])->name('settings.profile.remove-photo');
        Route::post('/settings/profile/update-password', [SettingsController::class, 'updatePassword'])->name('settings.profile.update-password');
        Route::get('/settings/theme', [SettingsController::class, 'theme'])->name('settings.theme');
        Route::post('/settings/theme', [SettingsController::class, 'updateTheme'])->name('settings.theme.update');
        Route::get('/settings/company', [SettingsController::class, 'company'])->name('settings.company');
        Route::post('/settings/company', [SettingsController::class, 'updateCompany'])->name('settings.company.update');
        Route::get('/settings/app', [SettingsController::class, 'app'])->name('settings.app');
        Route::post('/settings/app', [SettingsController::class, 'updateApp'])->name('settings.app.update');
        Route::get('/settings/email', [SettingsController::class, 'email'])->name('settings.email');
        Route::post('/settings/email', [SettingsController::class, 'updateEmail'])->name('settings.email.update');
        Route::get('/settings/email/test', [SettingsController::class, 'emailTest'])->name('settings.email.test');
        Route::get('/settings/security', [SettingsController::class, 'security'])->name('settings.security');
        Route::post('/settings/security', [SettingsController::class, 'updateSecurity'])->name('settings.security.update');
        Route::get('/settings/backup', [SettingsController::class, 'backup'])->name('settings.backup');
        Route::post('/settings/backup', [SettingsController::class, 'updateBackup'])->name('settings.backup.update');
        Route::get('/settings/updates', [SettingsController::class, 'updates'])->name('settings.updates');
        Route::post('/settings/updates', [SettingsController::class, 'uploadUpdate'])->name('settings.updates.upload');
        Route::post('/settings/updates/settings', [SettingsController::class, 'updateUpdateSettings'])->name('settings.updates.settings');
        Route::get('/settings/tax', [SettingsController::class, 'tax'])->name('settings.tax');
        Route::post('/settings/tax', [SettingsController::class, 'updateTax'])->name('settings.tax.update');
        
        // Profile Routes
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    }
);

// Test route for easy access to import functionality
Route::get('/quick-login', function () {
    try {
        $adminUser = App\Models\User::with('role')->whereHas('role', function($q) {
            $q->where('name', 'Admin');
        })->first();
        
        if ($adminUser) {
            Auth::login($adminUser);
            return redirect('/products/import')->with('success', 'Logged in as ' . $adminUser->name . ' (Role: ' . $adminUser->role->name . ')');
        }
        
        return redirect('/login')->with('error', 'No admin user found');
    } catch (\Exception $e) {
        return response('Error: ' . $e->getMessage(), 500);
    }
})->name('quick.login');

// Temporary public route for products import (remove after fixing auth)
Route::get('/import-products', function () {
    return view('products.import.index');
})->name('public.import');

// Test route to verify products import view (temporary - remove after testing)
Route::get('/test-import', function () {
    try {
        return view('products.import.index');
    } catch (\Exception $e) {
        return response('View Error: ' . $e->getMessage(), 500);
    }
})->name('test.import');

require __DIR__.'/auth.php';
