import { test, expect } from '@playwright/test';
import { AuthHelper, TestDataHelper } from './utils/auth.js';

test.describe('Customers Module', () => {
  let authHelper;

  test.beforeEach(async ({ page }) => {
    authHelper = new AuthHelper(page);
    await authHelper.loginAsAdmin();
  });

  test('should display customers index page correctly', async ({ page }) => {
    await page.goto('/customers');
    
    // Check page elements
    await expect(page.locator('h1')).toContainText('Customers');
    await expect(page.locator('text=Add New Customer')).toBeVisible();
    await expect(page.locator('input[placeholder*="Search"]')).toBeVisible();
    await expect(page.locator('.customer-card')).toBeVisible();
  });

  test('should navigate to create customer page', async ({ page }) => {
    await page.goto('/customers');
    
    await page.click('text=Add New Customer');
    await expect(page).toHaveURL('/customers/create');
    await expect(page.locator('h1')).toContainText('Add New Customer');
  });

  test('should display create customer form correctly', async ({ page }) => {
    await page.goto('/customers/create');
    
    // Check form elements
    await expect(page.locator('input[name="name"]')).toBeVisible();
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="phone"]')).toBeVisible();
    await expect(page.locator('textarea[name="address"]')).toBeVisible();
    await expect(page.locator('input[name="credit_limit"]')).toBeVisible();
    await expect(page.locator('select[name="status"]')).toBeVisible();
  });

  test('should validate required fields on customer creation', async ({ page }) => {
    await page.goto('/customers/create');
    
    // Try to submit empty form
    await page.click('button[type="submit"]');
    
    // Check for validation errors
    await expect(page.locator('text=The name field is required')).toBeVisible();
    await expect(page.locator('text=The email field is required')).toBeVisible();
    await expect(page.locator('text=The phone field is required')).toBeVisible();
  });

  test('should create customer successfully', async ({ page }) => {
    await page.goto('/customers/create');
    
    const customerData = TestDataHelper.generateCustomer();
    
    // Fill form
    await page.fill('input[name="name"]', customerData.name);
    await page.fill('input[name="email"]', customerData.email);
    await page.fill('input[name="phone"]', customerData.phone);
    await page.fill('textarea[name="address"]', customerData.address);
    await page.fill('input[name="credit_limit"]', '5000');
    await page.selectOption('select[name="status"]', 'active');
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Check for success message and redirect
    await expect(page.locator('.alert-success')).toContainText('Customer created successfully');
    await expect(page).toHaveURL(/\/customers\/\d+/);
  });

  test('should search customers by name', async ({ page }) => {
    await page.goto('/customers');
    
    // Search for customers
    await page.fill('input[placeholder*="Search"]', 'John');
    await page.click('button:has-text("Search")');
    
    // Wait for results
    await page.waitForTimeout(1000);
    
    // Check if search results contain the search term
    const customerCards = page.locator('.customer-card');
    const firstCard = customerCards.first();
    await expect(firstCard).toContainText('John');
  });

  test('should view customer details', async ({ page }) => {
    await page.goto('/customers');
    
    // Click on first customer view button
    await page.click('.customer-card:first-child .btn:has-text("View")');
    
    // Check customer details page
    await expect(page).toHaveURL(/\/customers\/\d+/);
    await expect(page.locator('h1')).toContainText('Customer Details');
    await expect(page.locator('.customer-info')).toBeVisible();
    await expect(page.locator('.recent-orders')).toBeVisible();
  });

  test('should display customer analytics button', async ({ page }) => {
    await page.goto('/customers');
    
    // Click on first customer view button
    await page.click('.customer-card:first-child .btn:has-text("View")');
    
    // Check if analytics button is visible
    await expect(page.locator('.btn:has-text("View Analytics")')).toBeVisible();
  });

  test('should open customer analytics modal', async ({ page }) => {
    await page.goto('/customers');
    
    // Click on first customer view button
    await page.click('.customer-card:first-child .btn:has-text("View")');
    
    // Click analytics button
    await page.click('.btn:has-text("View Analytics")');
    
    // Check if modal opens
    await expect(page.locator('#customerAnalyticsModal')).toBeVisible();
    await expect(page.locator('.modal-title')).toContainText('Customer Analytics & Due Summary');
  });

  test('should display analytics data in modal', async ({ page }) => {
    await page.goto('/customers');
    
    // Click on first customer view button
    await page.click('.customer-card:first-child .btn:has-text("View")');
    
    // Click analytics button
    await page.click('.btn:has-text("View Analytics")');
    
    // Wait for modal to load data
    await page.waitForTimeout(2000);
    
    // Check if analytics sections are visible
    await expect(page.locator('.modal-body .card:has-text("Outstanding Summary")')).toBeVisible();
    await expect(page.locator('.modal-body .card:has-text("Credit Summary")')).toBeVisible();
    await expect(page.locator('.modal-body .card:has-text("Aging Analysis")')).toBeVisible();
    await expect(page.locator('.modal-body .card:has-text("Payment History")')).toBeVisible();
    await expect(page.locator('.modal-body .card:has-text("Recent Transactions")')).toBeVisible();
  });

  test('should display outstanding amount in analytics', async ({ page }) => {
    await page.goto('/customers');
    
    // Click on first customer view button
    await page.click('.customer-card:first-child .btn:has-text("View")');
    
    // Click analytics button
    await page.click('.btn:has-text("View Analytics")');
    
    // Wait for modal to load data
    await page.waitForTimeout(2000);
    
    // Check if outstanding amount is displayed
    await expect(page.locator('.modal-body')).toContainText('Total Outstanding');
    await expect(page.locator('.modal-body')).toContainText('৳');
  });

  test('should display credit utilization in analytics', async ({ page }) => {
    await page.goto('/customers');
    
    // Click on first customer view button
    await page.click('.customer-card:first-child .btn:has-text("View")');
    
    // Click analytics button
    await page.click('.btn:has-text("View Analytics")');
    
    // Wait for modal to load data
    await page.waitForTimeout(2000);
    
    // Check if credit utilization is displayed
    await expect(page.locator('.modal-body')).toContainText('Credit Limit');
    await expect(page.locator('.modal-body')).toContainText('Credit Used');
    await expect(page.locator('.modal-body')).toContainText('Utilization');
    await expect(page.locator('.progress-bar')).toBeVisible();
  });

  test('should display aging analysis table', async ({ page }) => {
    await page.goto('/customers');
    
    // Click on first customer view button
    await page.click('.customer-card:first-child .btn:has-text("View")');
    
    // Click analytics button
    await page.click('.btn:has-text("View Analytics")');
    
    // Wait for modal to load data
    await page.waitForTimeout(2000);
    
    // Check aging analysis table
    await expect(page.locator('.modal-body table')).toBeVisible();
    await expect(page.locator('.modal-body')).toContainText('Current (0-30 days)');
    await expect(page.locator('.modal-body')).toContainText('31-60 days');
    await expect(page.locator('.modal-body')).toContainText('61-90 days');
    await expect(page.locator('.modal-body')).toContainText('91-120 days');
    await expect(page.locator('.modal-body')).toContainText('Over 120 days');
  });

  test('should display payment history', async ({ page }) => {
    await page.goto('/customers');
    
    // Click on first customer view button
    await page.click('.customer-card:first-child .btn:has-text("View")');
    
    // Click analytics button
    await page.click('.btn:has-text("View Analytics")');
    
    // Wait for modal to load data
    await page.waitForTimeout(2000);
    
    // Check payment history section
    await expect(page.locator('.modal-body')).toContainText('Last 6 Months Paid');
    await expect(page.locator('.modal-body')).toContainText('Payment Count');
    await expect(page.locator('.modal-body')).toContainText('Average Payment');
    await expect(page.locator('.modal-body')).toContainText('Last Payment');
  });

  test('should display recent transactions table', async ({ page }) => {
    await page.goto('/customers');
    
    // Click on first customer view button
    await page.click('.customer-card:first-child .btn:has-text("View")');
    
    // Click analytics button
    await page.click('.btn:has-text("View Analytics")');
    
    // Wait for modal to load data
    await page.waitForTimeout(2000);
    
    // Check recent transactions table
    const transactionsTable = page.locator('.modal-body .card:has-text("Recent Transactions") table');
    await expect(transactionsTable).toBeVisible();
    await expect(transactionsTable).toContainText('Date');
    await expect(transactionsTable).toContainText('Type');
    await expect(transactionsTable).toContainText('Description');
    await expect(transactionsTable).toContainText('Amount');
    await expect(transactionsTable).toContainText('Status');
  });

  test('should close analytics modal', async ({ page }) => {
    await page.goto('/customers');
    
    // Click on first customer view button
    await page.click('.customer-card:first-child .btn:has-text("View")');
    
    // Click analytics button
    await page.click('.btn:has-text("View Analytics")');
    
    // Wait for modal to open
    await expect(page.locator('#customerAnalyticsModal')).toBeVisible();
    
    // Close modal
    await page.click('.btn-close');
    
    // Check if modal is closed
    await expect(page.locator('#customerAnalyticsModal')).not.toBeVisible();
  });

  test('should edit customer', async ({ page }) => {
    await page.goto('/customers');
    
    // Click on first customer edit button
    await page.click('.customer-card:first-child .btn:has-text("Edit")');
    
    // Check edit form
    await expect(page).toHaveURL(/\/customers\/\d+\/edit/);
    await expect(page.locator('h1')).toContainText('Edit Customer');
    
    // Update customer name
    await page.fill('input[name="name"]', 'Updated Customer Name');
    
    // Submit update
    await page.click('button[type="submit"]');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('Customer updated successfully');
  });

  test('should delete customer', async ({ page }) => {
    await page.goto('/customers');
    
    // Get initial customer count
    const initialCount = await page.locator('.customer-card').count();
    
    // Click delete button on first customer
    await page.click('.customer-card:first-child .btn-danger');
    
    // Confirm deletion in modal
    await page.click('.modal .btn-danger:has-text("Delete")');
    
    // Wait for deletion to complete
    await page.waitForTimeout(1000);
    
    // Check if customer count decreased
    const newCount = await page.locator('.customer-card').count();
    expect(newCount).toBe(initialCount - 1);
  });

  test('should validate email format', async ({ page }) => {
    await page.goto('/customers/create');
    
    // Fill invalid email
    await page.fill('input[name="email"]', 'invalid-email');
    await page.click('button[type="submit"]');
    
    // Check for email validation error
    await expect(page.locator('text=The email must be a valid email address')).toBeVisible();
  });

  test('should validate credit limit as numeric', async ({ page }) => {
    await page.goto('/customers/create');
    
    // Fill non-numeric credit limit
    await page.fill('input[name="credit_limit"]', 'abc');
    await page.click('button[type="submit"]');
    
    // Check for numeric validation error
    await expect(page.locator('text=The credit limit must be a number')).toBeVisible();
  });

  test('should export customers to PDF', async ({ page }) => {
    await page.goto('/customers');
    
    // Start download
    const downloadPromise = page.waitForEvent('download');
    await page.click('.btn:has-text("Export PDF")');
    const download = await downloadPromise;
    
    // Verify download
    expect(download.suggestedFilename()).toContain('.pdf');
  });

  test('should export customers to Excel', async ({ page }) => {
    await page.goto('/customers');
    
    // Start download
    const downloadPromise = page.waitForEvent('download');
    await page.click('.btn:has-text("Export Excel")');
    const download = await downloadPromise;
    
    // Verify download
    expect(download.suggestedFilename()).toContain('.xlsx');
  });

  test('should handle API error in analytics modal gracefully', async ({ page }) => {
    // Mock API to return error
    await page.route('**/api/customers/*/summary', route => {
      route.fulfill({
        status: 500,
        contentType: 'application/json',
        body: JSON.stringify({ error: 'Internal Server Error' })
      });
    });
    
    await page.goto('/customers');
    
    // Click on first customer view button
    await page.click('.customer-card:first-child .btn:has-text("View")');
    
    // Click analytics button
    await page.click('.btn:has-text("View Analytics")');
    
    // Wait for error message
    await page.waitForTimeout(2000);
    
    // Check if error message is displayed
    await expect(page.locator('.alert-danger')).toContainText('Error loading customer analytics');
  });

  test('should show customer statistics', async ({ page }) => {
    await page.goto('/customers');
    
    // Check if statistics cards are visible
    await expect(page.locator('.stats-card:has-text("Total Customers")')).toBeVisible();
    await expect(page.locator('.stats-card:has-text("Active Customers")')).toBeVisible();
    await expect(page.locator('.stats-card:has-text("Inactive Customers")')).toBeVisible();
    await expect(page.locator('.stats-card:has-text("Total Credit Limit")')).toBeVisible();
  });

  test('should create first order link work', async ({ page }) => {
    await page.goto('/customers');
    
    // Click on first customer view button
    await page.click('.customer-card:first-child .btn:has-text("View")');
    
    // Check if create first order link is visible (for customers with no orders)
    const createOrderLink = page.locator('text=Create their first order');
    if (await createOrderLink.isVisible()) {
      await createOrderLink.click();
      await expect(page).toHaveURL(/\/orders\/create/);
    }
  });
});