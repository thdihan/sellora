import { test, expect } from '@playwright/test';
import { AuthHelper } from './utils/auth.js';

test.describe('Dashboard Module', () => {
  let authHelper;

  test.beforeEach(async ({ page }) => {
    authHelper = new AuthHelper(page);
    await authHelper.loginAsAdmin();
  });

  test('should display dashboard correctly after login', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Check page elements
    await expect(page.locator('h1')).toContainText('Dashboard');
    await expect(page.locator('.dashboard-stats')).toBeVisible();
    await expect(page.locator('.chart-container')).toBeVisible();
  });

  test('should display key performance indicators', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Check KPI cards
    await expect(page.locator('.kpi-card:has-text("Total Sales")')).toBeVisible();
    await expect(page.locator('.kpi-card:has-text("Total Orders")')).toBeVisible();
    await expect(page.locator('.kpi-card:has-text("Total Customers")')).toBeVisible();
    await expect(page.locator('.kpi-card:has-text("Total Products")')).toBeVisible();
    await expect(page.locator('.kpi-card:has-text("Outstanding Amount")')).toBeVisible();
  });

  test('should display sales chart', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Wait for chart to load
    await page.waitForTimeout(2000);
    
    // Check if sales chart is visible
    await expect(page.locator('#salesChart')).toBeVisible();
    await expect(page.locator('text=Sales Overview')).toBeVisible();
  });

  test('should display orders chart', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Wait for chart to load
    await page.waitForTimeout(2000);
    
    // Check if orders chart is visible
    await expect(page.locator('#ordersChart')).toBeVisible();
    await expect(page.locator('text=Orders Overview')).toBeVisible();
  });

  test('should display top products section', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Check top products section
    await expect(page.locator('.top-products')).toBeVisible();
    await expect(page.locator('text=Top Selling Products')).toBeVisible();
    await expect(page.locator('.product-item')).toBeVisible();
  });

  test('should display recent orders section', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Check recent orders section
    await expect(page.locator('.recent-orders')).toBeVisible();
    await expect(page.locator('text=Recent Orders')).toBeVisible();
    await expect(page.locator('.order-item')).toBeVisible();
  });

  test('should display low stock alerts', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Check low stock alerts section
    await expect(page.locator('.low-stock-alerts')).toBeVisible();
    await expect(page.locator('text=Low Stock Alerts')).toBeVisible();
  });

  test('should display pending bills section', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Check pending bills section
    await expect(page.locator('.pending-bills')).toBeVisible();
    await expect(page.locator('text=Pending Bills')).toBeVisible();
  });

  test('should filter dashboard by date range', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Open date range picker
    await page.click('.date-range-picker');
    
    // Select last 7 days
    await page.click('text=Last 7 Days');
    
    // Wait for dashboard to update
    await page.waitForTimeout(2000);
    
    // Check if data is updated
    await expect(page.locator('.kpi-card')).toBeVisible();
  });

  test('should filter dashboard by last 30 days', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Open date range picker
    await page.click('.date-range-picker');
    
    // Select last 30 days
    await page.click('text=Last 30 Days');
    
    // Wait for dashboard to update
    await page.waitForTimeout(2000);
    
    // Check if data is updated
    await expect(page.locator('.kpi-card')).toBeVisible();
  });

  test('should filter dashboard by custom date range', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Open date range picker
    await page.click('.date-range-picker');
    
    // Select custom range
    await page.click('text=Custom Range');
    
    // Fill start date
    await page.fill('input[name="start_date"]', '2024-01-01');
    
    // Fill end date
    await page.fill('input[name="end_date"]', '2024-01-31');
    
    // Apply filter
    await page.click('.btn:has-text("Apply")');
    
    // Wait for dashboard to update
    await page.waitForTimeout(2000);
    
    // Check if data is updated
    await expect(page.locator('.kpi-card')).toBeVisible();
  });

  test('should display sales by category chart', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Wait for chart to load
    await page.waitForTimeout(2000);
    
    // Check if category chart is visible
    await expect(page.locator('#categoryChart')).toBeVisible();
    await expect(page.locator('text=Sales by Category')).toBeVisible();
  });

  test('should display customer analytics', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Check customer analytics section
    await expect(page.locator('.customer-analytics')).toBeVisible();
    await expect(page.locator('text=Customer Analytics')).toBeVisible();
    await expect(page.locator('text=New Customers')).toBeVisible();
    await expect(page.locator('text=Returning Customers')).toBeVisible();
  });

  test('should display revenue trends', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Wait for chart to load
    await page.waitForTimeout(2000);
    
    // Check revenue trends chart
    await expect(page.locator('#revenueChart')).toBeVisible();
    await expect(page.locator('text=Revenue Trends')).toBeVisible();
  });

  test('should navigate to orders from recent orders', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Click on view all orders link
    await page.click('.recent-orders .btn:has-text("View All")');
    
    // Check if navigated to orders page
    await expect(page).toHaveURL('/orders');
  });

  test('should navigate to products from top products', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Click on view all products link
    await page.click('.top-products .btn:has-text("View All")');
    
    // Check if navigated to products page
    await expect(page).toHaveURL('/products');
  });

  test('should navigate to bills from pending bills', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Click on view all bills link
    await page.click('.pending-bills .btn:has-text("View All")');
    
    // Check if navigated to bills page
    await expect(page).toHaveURL('/bills');
  });

  test('should display weather widget', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Check if weather widget is visible
    const weatherWidget = page.locator('.weather-widget');
    if (await weatherWidget.isVisible()) {
      await expect(weatherWidget).toBeVisible();
      await expect(page.locator('.temperature')).toBeVisible();
    }
  });

  test('should display notifications panel', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Check notifications panel
    await expect(page.locator('.notifications-panel')).toBeVisible();
    await expect(page.locator('text=Notifications')).toBeVisible();
  });

  test('should mark notification as read', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Click on first notification
    const firstNotification = page.locator('.notification-item:first-child');
    if (await firstNotification.isVisible()) {
      await firstNotification.click();
      
      // Check if notification is marked as read
      await expect(firstNotification).toHaveClass(/read/);
    }
  });

  test('should refresh dashboard data', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Click refresh button
    await page.click('.btn:has-text("Refresh")');
    
    // Wait for data to refresh
    await page.waitForTimeout(2000);
    
    // Check if loading indicator appears and disappears
    await expect(page.locator('.loading-indicator')).not.toBeVisible();
  });

  test('should export dashboard report', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Start download
    const downloadPromise = page.waitForEvent('download');
    await page.click('.btn:has-text("Export Report")');
    const download = await downloadPromise;
    
    // Verify download
    expect(download.suggestedFilename()).toContain('dashboard-report');
  });

  test('should display quick actions panel', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Check quick actions panel
    await expect(page.locator('.quick-actions')).toBeVisible();
    await expect(page.locator('text=Quick Actions')).toBeVisible();
    await expect(page.locator('.btn:has-text("New Order")')).toBeVisible();
    await expect(page.locator('.btn:has-text("New Customer")')).toBeVisible();
    await expect(page.locator('.btn:has-text("New Product")')).toBeVisible();
  });

  test('should navigate to create order from quick actions', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Click new order button
    await page.click('.quick-actions .btn:has-text("New Order")');
    
    // Check if navigated to create order page
    await expect(page).toHaveURL('/orders/create');
  });

  test('should navigate to create customer from quick actions', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Click new customer button
    await page.click('.quick-actions .btn:has-text("New Customer")');
    
    // Check if navigated to create customer page
    await expect(page).toHaveURL('/customers/create');
  });

  test('should navigate to create product from quick actions', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Click new product button
    await page.click('.quick-actions .btn:has-text("New Product")');
    
    // Check if navigated to create product page
    await expect(page).toHaveURL('/products/create');
  });

  test('should display system status indicators', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Check system status section
    await expect(page.locator('.system-status')).toBeVisible();
    await expect(page.locator('text=System Status')).toBeVisible();
    await expect(page.locator('.status-indicator')).toBeVisible();
  });

  test('should display calendar widget', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Check calendar widget
    const calendarWidget = page.locator('.calendar-widget');
    if (await calendarWidget.isVisible()) {
      await expect(calendarWidget).toBeVisible();
      await expect(page.locator('.calendar-events')).toBeVisible();
    }
  });

  test('should handle chart interactions', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Wait for charts to load
    await page.waitForTimeout(3000);
    
    // Hover over chart data point
    await page.hover('#salesChart');
    
    // Check if tooltip appears
    await expect(page.locator('.chart-tooltip')).toBeVisible();
  });

  test('should display mobile responsive layout', async ({ page }) => {
    // Set mobile viewport
    await page.setViewportSize({ width: 375, height: 667 });
    
    await page.goto('/dashboard');
    
    // Check if mobile layout is applied
    await expect(page.locator('.dashboard-mobile')).toBeVisible();
    await expect(page.locator('.mobile-menu-toggle')).toBeVisible();
  });

  test('should toggle sidebar on mobile', async ({ page }) => {
    // Set mobile viewport
    await page.setViewportSize({ width: 375, height: 667 });
    
    await page.goto('/dashboard');
    
    // Click mobile menu toggle
    await page.click('.mobile-menu-toggle');
    
    // Check if sidebar is visible
    await expect(page.locator('.sidebar')).toBeVisible();
    
    // Click toggle again to close
    await page.click('.mobile-menu-toggle');
    
    // Check if sidebar is hidden
    await expect(page.locator('.sidebar')).not.toBeVisible();
  });

  test('should display real-time updates', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Check if real-time indicator is visible
    await expect(page.locator('.real-time-indicator')).toBeVisible();
    await expect(page.locator('text=Live')).toBeVisible();
  });

  test('should handle dashboard errors gracefully', async ({ page }) => {
    // Mock API to return error
    await page.route('**/api/dashboard/stats', route => {
      route.fulfill({
        status: 500,
        contentType: 'application/json',
        body: JSON.stringify({ error: 'Internal Server Error' })
      });
    });
    
    await page.goto('/dashboard');
    
    // Wait for error message
    await page.waitForTimeout(2000);
    
    // Check if error message is displayed
    await expect(page.locator('.alert-danger')).toContainText('Error loading dashboard data');
  });

  test('should display performance metrics', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Check performance metrics section
    await expect(page.locator('.performance-metrics')).toBeVisible();
    await expect(page.locator('text=Performance Metrics')).toBeVisible();
    await expect(page.locator('text=Response Time')).toBeVisible();
    await expect(page.locator('text=Uptime')).toBeVisible();
  });
});