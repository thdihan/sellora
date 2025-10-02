import { test, expect } from '@playwright/test';
import { AuthHelper, TestDataHelper } from './utils/auth.js';

test.describe('Orders Module', () => {
  let authHelper;

  test.beforeEach(async ({ page }) => {
    authHelper = new AuthHelper(page);
    await authHelper.loginAsAdmin();
  });

  test('should display orders index page correctly', async ({ page }) => {
    await page.goto('/orders');
    
    // Check page elements
    await expect(page.locator('h1')).toContainText('Orders');
    await expect(page.locator('text=Add New Order')).toBeVisible();
    await expect(page.locator('input[placeholder*="Search"]')).toBeVisible();
    await expect(page.locator('select[name="status"]')).toBeVisible();
  });

  test('should navigate to create order page', async ({ page }) => {
    await page.goto('/orders');
    
    await page.click('text=Add New Order');
    await expect(page).toHaveURL('/orders/create');
    await expect(page.locator('h1')).toContainText('Add New Order');
  });

  test('should display create order form correctly', async ({ page }) => {
    await page.goto('/orders/create');
    
    // Check form elements
    await expect(page.locator('input[name="customer_name"]')).toBeVisible();
    await expect(page.locator('input[name="customer_email"]')).toBeVisible();
    await expect(page.locator('input[name="customer_phone"]')).toBeVisible();
    await expect(page.locator('textarea[name="customer_address"]')).toBeVisible();
    await expect(page.locator('input[name="product_name"]')).toBeVisible();
    await expect(page.locator('input[name="quantity"]')).toBeVisible();
    await expect(page.locator('input[name="unit_price"]')).toBeVisible();
    await expect(page.locator('select[name="payment_method"]')).toBeVisible();
  });

  test('should validate required fields on order creation', async ({ page }) => {
    await page.goto('/orders/create');
    
    // Try to submit empty form
    await page.click('button[type="submit"]');
    
    // Check for validation errors
    await expect(page.locator('text=The customer name field is required')).toBeVisible();
    await expect(page.locator('text=The customer email field is required')).toBeVisible();
    await expect(page.locator('text=The product name field is required')).toBeVisible();
    await expect(page.locator('text=The quantity field is required')).toBeVisible();
    await expect(page.locator('text=The unit price field is required')).toBeVisible();
  });

  test('should test customer typeahead functionality', async ({ page }) => {
    await page.goto('/orders/create');
    
    // Type in customer name field to trigger typeahead
    await page.fill('input[name="customer_name"]', 'John');
    
    // Wait for typeahead suggestions
    await page.waitForSelector('.typeahead-suggestions', { timeout: 5000 });
    
    // Check if suggestions appear
    const suggestions = page.locator('.typeahead-suggestions .suggestion-item');
    await expect(suggestions.first()).toBeVisible();
    
    // Click on first suggestion
    await suggestions.first().click();
    
    // Verify auto-fill functionality
    await expect(page.locator('input[name="customer_email"]')).not.toHaveValue('');
    await expect(page.locator('input[name="customer_phone"]')).not.toHaveValue('');
  });

  test('should test product typeahead functionality', async ({ page }) => {
    await page.goto('/orders/create');
    
    // Type in product name field to trigger typeahead
    await page.fill('input[name="product_name"]', 'Laptop');
    
    // Wait for typeahead suggestions
    await page.waitForSelector('.typeahead-suggestions', { timeout: 5000 });
    
    // Check if suggestions appear
    const suggestions = page.locator('.typeahead-suggestions .suggestion-item');
    await expect(suggestions.first()).toBeVisible();
    
    // Click on first suggestion
    await suggestions.first().click();
    
    // Verify auto-fill functionality
    await expect(page.locator('textarea[name="product_description"]')).not.toHaveValue('');
    await expect(page.locator('input[name="unit_price"]')).not.toHaveValue('');
  });

  test('should calculate total amount automatically', async ({ page }) => {
    await page.goto('/orders/create');
    
    // Fill quantity and unit price
    await page.fill('input[name="quantity"]', '5');
    await page.fill('input[name="unit_price"]', '100');
    
    // Trigger calculation by clicking elsewhere
    await page.click('input[name="customer_name"]');
    
    // Check if total is calculated (5 * 100 = 500)
    await expect(page.locator('#calculated-amount')).toContainText('500');
  });

  test('should apply discount correctly', async ({ page }) => {
    await page.goto('/orders/create');
    
    // Fill order details
    await page.fill('input[name="quantity"]', '10');
    await page.fill('input[name="unit_price"]', '50');
    await page.fill('input[name="discount"]', '10');
    
    // Trigger calculation
    await page.click('input[name="customer_name"]');
    
    // Check if discount is applied (10 * 50 - 10 = 490)
    await expect(page.locator('#calculated-amount')).toContainText('490');
  });

  test('should create order successfully', async ({ page }) => {
    await page.goto('/orders/create');
    
    const orderData = TestDataHelper.generateOrder();
    
    // Fill form
    await page.fill('input[name="customer_name"]', orderData.customer_name);
    await page.fill('input[name="customer_email"]', orderData.customer_email);
    await page.fill('input[name="customer_phone"]', orderData.customer_phone);
    await page.fill('textarea[name="customer_address"]', orderData.customer_address);
    await page.fill('input[name="product_name"]', orderData.product_name);
    await page.fill('input[name="quantity"]', orderData.quantity.toString());
    await page.fill('input[name="unit_price"]', orderData.unit_price.toString());
    await page.selectOption('select[name="payment_method"]', 'cash');
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Check for success message and redirect
    await expect(page.locator('.alert-success')).toContainText('Order created successfully');
    await expect(page).toHaveURL(/\/orders\/\d+/);
  });

  test('should search orders by customer name', async ({ page }) => {
    await page.goto('/orders');
    
    // Search for orders
    await page.fill('input[placeholder*="Search"]', 'John');
    await page.click('button:has-text("Search")');
    
    // Wait for results
    await page.waitForTimeout(1000);
    
    // Check if search results contain the search term
    const orderRows = page.locator('tbody tr');
    const firstRow = orderRows.first();
    await expect(firstRow).toContainText('John');
  });

  test('should filter orders by status', async ({ page }) => {
    await page.goto('/orders');
    
    // Filter by pending status
    await page.selectOption('select[name="status"]', 'pending');
    await page.click('button:has-text("Search")');
    
    // Wait for results
    await page.waitForTimeout(1000);
    
    // Check if all visible orders have pending status
    const statusBadges = page.locator('.badge:has-text("pending")');
    await expect(statusBadges.first()).toBeVisible();
  });

  test('should view order details', async ({ page }) => {
    await page.goto('/orders');
    
    // Click on first order view button
    await page.click('tbody tr:first-child .btn:has-text("View")');
    
    // Check order details page
    await expect(page).toHaveURL(/\/orders\/\d+/);
    await expect(page.locator('h1')).toContainText('Order Details');
    await expect(page.locator('.customer-info')).toBeVisible();
    await expect(page.locator('.order-items')).toBeVisible();
  });

  test('should edit order', async ({ page }) => {
    await page.goto('/orders');
    
    // Click on first order edit button
    await page.click('tbody tr:first-child .btn:has-text("Edit")');
    
    // Check edit form
    await expect(page).toHaveURL(/\/orders\/\d+\/edit/);
    await expect(page.locator('h1')).toContainText('Edit Order');
    
    // Update customer name
    await page.fill('input[name="customer_name"]', 'Updated Customer Name');
    
    // Submit update
    await page.click('button[type="submit"]');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('Order updated successfully');
  });

  test('should delete order', async ({ page }) => {
    await page.goto('/orders');
    
    // Get initial order count
    const initialCount = await page.locator('tbody tr').count();
    
    // Click delete button on first order
    await page.click('tbody tr:first-child .btn-danger');
    
    // Confirm deletion in modal
    await page.click('.modal .btn-danger:has-text("Delete")');
    
    // Wait for deletion to complete
    await page.waitForTimeout(1000);
    
    // Check if order count decreased
    const newCount = await page.locator('tbody tr').count();
    expect(newCount).toBe(initialCount - 1);
  });

  test('should export orders to PDF', async ({ page }) => {
    await page.goto('/orders');
    
    // Start download
    const downloadPromise = page.waitForEvent('download');
    await page.click('.btn:has-text("Export PDF")');
    const download = await downloadPromise;
    
    // Verify download
    expect(download.suggestedFilename()).toContain('.pdf');
  });

  test('should export orders to Excel', async ({ page }) => {
    await page.goto('/orders');
    
    // Start download
    const downloadPromise = page.waitForEvent('download');
    await page.click('.btn:has-text("Export Excel")');
    const download = await downloadPromise;
    
    // Verify download
    expect(download.suggestedFilename()).toContain('.xlsx');
  });

  test('should validate email format in order creation', async ({ page }) => {
    await page.goto('/orders/create');
    
    // Fill invalid email
    await page.fill('input[name="customer_email"]', 'invalid-email');
    await page.click('button[type="submit"]');
    
    // Check for email validation error
    await expect(page.locator('text=The customer email must be a valid email address')).toBeVisible();
  });

  test('should validate numeric fields', async ({ page }) => {
    await page.goto('/orders/create');
    
    // Fill non-numeric values
    await page.fill('input[name="quantity"]', 'abc');
    await page.fill('input[name="unit_price"]', 'xyz');
    await page.click('button[type="submit"]');
    
    // Check for numeric validation errors
    await expect(page.locator('text=The quantity must be a number')).toBeVisible();
    await expect(page.locator('text=The unit price must be a number')).toBeVisible();
  });
});