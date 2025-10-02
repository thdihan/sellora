import { test, expect } from '@playwright/test';
import { AuthHelper, TestDataHelper } from './utils/auth.js';
import path from 'path';

test.describe('Bills Module', () => {
  let authHelper;

  test.beforeEach(async ({ page }) => {
    authHelper = new AuthHelper(page);
    await authHelper.loginAsAdmin();
  });

  test('should display bills index page correctly', async ({ page }) => {
    await page.goto('/bills');
    
    // Check page elements
    await expect(page.locator('h1')).toContainText('Bills');
    await expect(page.locator('text=Add New Bill')).toBeVisible();
    await expect(page.locator('input[placeholder*="Search"]')).toBeVisible();
    await expect(page.locator('select[name="status"]')).toBeVisible();
    await expect(page.locator('select[name="category"]')).toBeVisible();
    await expect(page.locator('select[name="priority"]')).toBeVisible();
  });

  test('should navigate to create bill page', async ({ page }) => {
    await page.goto('/bills');
    
    await page.click('text=Add New Bill');
    await expect(page).toHaveURL('/bills/create');
    await expect(page.locator('h1')).toContainText('Add New Bill');
  });

  test('should display create bill form correctly', async ({ page }) => {
    await page.goto('/bills/create');
    
    // Check form elements
    await expect(page.locator('input[name="amount"]')).toBeVisible();
    await expect(page.locator('textarea[name="description"]')).toBeVisible();
    await expect(page.locator('select[name="category"]')).toBeVisible();
    await expect(page.locator('select[name="priority"]')).toBeVisible();
    await expect(page.locator('select[name="status"]')).toBeVisible();
    await expect(page.locator('input[name="due_date"]')).toBeVisible();
    await expect(page.locator('input[name="attachment"]')).toBeVisible();
  });

  test('should validate required fields on bill creation', async ({ page }) => {
    await page.goto('/bills/create');
    
    // Try to submit empty form
    await page.click('button[type="submit"]');
    
    // Check for validation errors
    await expect(page.locator('text=The amount field is required')).toBeVisible();
    await expect(page.locator('text=The description field is required')).toBeVisible();
    await expect(page.locator('text=The attachment field is required')).toBeVisible();
  });

  test('should validate attachment requirement', async ({ page }) => {
    await page.goto('/bills/create');
    
    const billData = TestDataHelper.generateBill();
    
    // Fill form without attachment
    await page.fill('input[name="amount"]', billData.amount.toString());
    await page.fill('textarea[name="description"]', billData.description);
    await page.selectOption('select[name="category"]', billData.category);
    await page.selectOption('select[name="priority"]', billData.priority);
    
    // Try to submit without attachment
    await page.click('button[type="submit"]');
    
    // Check for attachment validation error
    await expect(page.locator('text=The attachment field is required')).toBeVisible();
  });

  test('should create bill successfully with attachment', async ({ page }) => {
    await page.goto('/bills/create');
    
    const billData = TestDataHelper.generateBill();
    
    // Fill form
    await page.fill('input[name="amount"]', billData.amount.toString());
    await page.fill('textarea[name="description"]', billData.description);
    await page.selectOption('select[name="category"]', billData.category);
    await page.selectOption('select[name="priority"]', billData.priority);
    await page.selectOption('select[name="status"]', 'pending');
    
    // Set due date (7 days from now)
    const futureDate = new Date();
    futureDate.setDate(futureDate.getDate() + 7);
    const dateString = futureDate.toISOString().split('T')[0];
    await page.fill('input[name="due_date"]', dateString);
    
    // Upload attachment (create a test file)
    const testFilePath = path.join(process.cwd(), 'tests/e2e/fixtures/test-bill.pdf');
    await page.setInputFiles('input[name="attachment"]', testFilePath);
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Check for success message and redirect
    await expect(page.locator('.alert-success')).toContainText('Bill created successfully');
    await expect(page).toHaveURL(/\/bills\/\d+/);
  });

  test('should search bills by description', async ({ page }) => {
    await page.goto('/bills');
    
    // Search for bills
    await page.fill('input[placeholder*="Search"]', 'Office');
    await page.click('button:has-text("Search")');
    
    // Wait for results
    await page.waitForTimeout(1000);
    
    // Check if search results contain the search term
    const billRows = page.locator('tbody tr');
    const firstRow = billRows.first();
    await expect(firstRow).toContainText('Office');
  });

  test('should filter bills by status', async ({ page }) => {
    await page.goto('/bills');
    
    // Filter by pending status
    await page.selectOption('select[name="status"]', 'pending');
    await page.click('button:has-text("Search")');
    
    // Wait for results
    await page.waitForTimeout(1000);
    
    // Check if all visible bills have pending status
    const statusBadges = page.locator('.badge:has-text("pending")');
    await expect(statusBadges.first()).toBeVisible();
  });

  test('should filter bills by category', async ({ page }) => {
    await page.goto('/bills');
    
    // Filter by Office Supplies category
    await page.selectOption('select[name="category"]', 'Office Supplies');
    await page.click('button:has-text("Search")');
    
    // Wait for results
    await page.waitForTimeout(1000);
    
    // Check if results contain the selected category
    const categoryText = page.locator('tbody tr:first-child');
    await expect(categoryText).toContainText('Office Supplies');
  });

  test('should filter bills by priority', async ({ page }) => {
    await page.goto('/bills');
    
    // Filter by high priority
    await page.selectOption('select[name="priority"]', 'high');
    await page.click('button:has-text("Search")');
    
    // Wait for results
    await page.waitForTimeout(1000);
    
    // Check if results contain high priority bills
    const priorityBadges = page.locator('.badge:has-text("high")');
    await expect(priorityBadges.first()).toBeVisible();
  });

  test('should clear filters', async ({ page }) => {
    await page.goto('/bills');
    
    // Apply filters
    await page.fill('input[placeholder*="Search"]', 'test');
    await page.selectOption('select[name="status"]', 'pending');
    await page.selectOption('select[name="category"]', 'Office Supplies');
    
    // Clear filters
    await page.click('button:has-text("Clear")');
    
    // Check if filters are cleared
    await expect(page.locator('input[placeholder*="Search"]')).toHaveValue('');
    await expect(page.locator('select[name="status"]')).toHaveValue('');
    await expect(page.locator('select[name="category"]')).toHaveValue('');
  });

  test('should view bill details', async ({ page }) => {
    await page.goto('/bills');
    
    // Click on first bill view button
    await page.click('tbody tr:first-child .btn:has-text("View")');
    
    // Check bill details page
    await expect(page).toHaveURL(/\/bills\/\d+/);
    await expect(page.locator('h1')).toContainText('Bill Details');
    await expect(page.locator('.bill-info')).toBeVisible();
    await expect(page.locator('.attachment-section')).toBeVisible();
  });

  test('should edit bill', async ({ page }) => {
    await page.goto('/bills');
    
    // Click on first bill edit button
    await page.click('tbody tr:first-child .btn:has-text("Edit")');
    
    // Check edit form
    await expect(page).toHaveURL(/\/bills\/\d+\/edit/);
    await expect(page.locator('h1')).toContainText('Edit Bill');
    
    // Update description
    await page.fill('textarea[name="description"]', 'Updated bill description');
    
    // Submit update
    await page.click('button[type="submit"]');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('Bill updated successfully');
  });

  test('should delete bill', async ({ page }) => {
    await page.goto('/bills');
    
    // Get initial bill count
    const initialCount = await page.locator('tbody tr').count();
    
    // Click delete button on first bill
    await page.click('tbody tr:first-child .btn-danger');
    
    // Confirm deletion in modal
    await page.click('.modal .btn-danger:has-text("Delete")');
    
    // Wait for deletion to complete
    await page.waitForTimeout(1000);
    
    // Check if bill count decreased
    const newCount = await page.locator('tbody tr').count();
    expect(newCount).toBe(initialCount - 1);
  });

  test('should download bill attachment', async ({ page }) => {
    await page.goto('/bills');
    
    // Click on first bill view button
    await page.click('tbody tr:first-child .btn:has-text("View")');
    
    // Start download
    const downloadPromise = page.waitForEvent('download');
    await page.click('.btn:has-text("Download Attachment")');
    const download = await downloadPromise;
    
    // Verify download
    expect(download.suggestedFilename()).toBeTruthy();
  });

  test('should export bills to PDF', async ({ page }) => {
    await page.goto('/bills');
    
    // Start download
    const downloadPromise = page.waitForEvent('download');
    await page.click('.btn:has-text("Export PDF")');
    const download = await downloadPromise;
    
    // Verify download
    expect(download.suggestedFilename()).toContain('.pdf');
  });

  test('should export bills to Excel', async ({ page }) => {
    await page.goto('/bills');
    
    // Start download
    const downloadPromise = page.waitForEvent('download');
    await page.click('.btn:has-text("Export Excel")');
    const download = await downloadPromise;
    
    // Verify download
    expect(download.suggestedFilename()).toContain('.xlsx');
  });

  test('should validate amount field as numeric', async ({ page }) => {
    await page.goto('/bills/create');
    
    // Fill non-numeric amount
    await page.fill('input[name="amount"]', 'abc');
    await page.click('button[type="submit"]');
    
    // Check for numeric validation error
    await expect(page.locator('text=The amount must be a number')).toBeVisible();
  });

  test('should validate due date is in future', async ({ page }) => {
    await page.goto('/bills/create');
    
    // Set past date
    const pastDate = new Date();
    pastDate.setDate(pastDate.getDate() - 1);
    const dateString = pastDate.toISOString().split('T')[0];
    await page.fill('input[name="due_date"]', dateString);
    
    await page.click('button[type="submit"]');
    
    // Check for date validation error
    await expect(page.locator('text=The due date must be a date after today')).toBeVisible();
  });

  test('should validate file type for attachment', async ({ page }) => {
    await page.goto('/bills/create');
    
    // Try to upload invalid file type
    const invalidFilePath = path.join(process.cwd(), 'tests/e2e/fixtures/test-image.txt');
    await page.setInputFiles('input[name="attachment"]', invalidFilePath);
    
    await page.click('button[type="submit"]');
    
    // Check for file type validation error
    await expect(page.locator('text=The attachment must be a file of type: pdf, doc, docx, jpg, jpeg, png')).toBeVisible();
  });

  test('should show bill statistics', async ({ page }) => {
    await page.goto('/bills');
    
    // Check if statistics cards are visible
    await expect(page.locator('.stats-card:has-text("Total Bills")')).toBeVisible();
    await expect(page.locator('.stats-card:has-text("Pending Bills")')).toBeVisible();
    await expect(page.locator('.stats-card:has-text("Paid Bills")')).toBeVisible();
    await expect(page.locator('.stats-card:has-text("Total Amount")')).toBeVisible();
  });

  test('should handle date range filtering', async ({ page }) => {
    await page.goto('/bills');
    
    // Set date range
    const startDate = new Date();
    startDate.setMonth(startDate.getMonth() - 1);
    const endDate = new Date();
    
    await page.fill('input[name="start_date"]', startDate.toISOString().split('T')[0]);
    await page.fill('input[name="end_date"]', endDate.toISOString().split('T')[0]);
    
    await page.click('button:has-text("Search")');
    
    // Wait for results
    await page.waitForTimeout(1000);
    
    // Check if results are filtered by date range
    const billRows = page.locator('tbody tr');
    await expect(billRows.first()).toBeVisible();
  });
});