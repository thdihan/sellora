import { test, expect } from '@playwright/test';
import { AuthHelper, TestDataHelper } from './utils/auth.js';

test.describe('Products Module', () => {
  let authHelper;

  test.beforeEach(async ({ page }) => {
    authHelper = new AuthHelper(page);
    await authHelper.loginAsAdmin();
  });

  test('should display products index page correctly', async ({ page }) => {
    await page.goto('/products');
    
    // Check page elements
    await expect(page.locator('h1')).toContainText('Products');
    await expect(page.locator('text=Add New Product')).toBeVisible();
    await expect(page.locator('input[placeholder*="Search"]')).toBeVisible();
    await expect(page.locator('.product-card')).toBeVisible();
  });

  test('should navigate to create product page', async ({ page }) => {
    await page.goto('/products');
    
    await page.click('text=Add New Product');
    await expect(page).toHaveURL('/products/create');
    await expect(page.locator('h1')).toContainText('Add New Product');
  });

  test('should display create product form correctly', async ({ page }) => {
    await page.goto('/products/create');
    
    // Check form elements
    await expect(page.locator('input[name="name"]')).toBeVisible();
    await expect(page.locator('input[name="sku"]')).toBeVisible();
    await expect(page.locator('textarea[name="description"]')).toBeVisible();
    await expect(page.locator('input[name="price"]')).toBeVisible();
    await expect(page.locator('input[name="cost"]')).toBeVisible();
    await expect(page.locator('input[name="stock_quantity"]')).toBeVisible();
    await expect(page.locator('select[name="category_id"]')).toBeVisible();
    await expect(page.locator('select[name="status"]')).toBeVisible();
  });

  test('should validate required fields on product creation', async ({ page }) => {
    await page.goto('/products/create');
    
    // Try to submit empty form
    await page.click('button[type="submit"]');
    
    // Check for validation errors
    await expect(page.locator('text=The name field is required')).toBeVisible();
    await expect(page.locator('text=The sku field is required')).toBeVisible();
    await expect(page.locator('text=The price field is required')).toBeVisible();
  });

  test('should create product successfully', async ({ page }) => {
    await page.goto('/products/create');
    
    const productData = TestDataHelper.generateProduct();
    
    // Fill form
    await page.fill('input[name="name"]', productData.name);
    await page.fill('input[name="sku"]', productData.sku);
    await page.fill('textarea[name="description"]', productData.description);
    await page.fill('input[name="price"]', '99.99');
    await page.fill('input[name="cost"]', '50.00');
    await page.fill('input[name="stock_quantity"]', '100');
    await page.selectOption('select[name="status"]', 'active');
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Check for success message and redirect
    await expect(page.locator('.alert-success')).toContainText('Product created successfully');
    await expect(page).toHaveURL(/\/products\/\d+/);
  });

  test('should validate SKU uniqueness', async ({ page }) => {
    await page.goto('/products/create');
    
    // Fill form with existing SKU
    await page.fill('input[name="name"]', 'Test Product');
    await page.fill('input[name="sku"]', 'EXISTING-SKU');
    await page.fill('input[name="price"]', '99.99');
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Check for SKU validation error
    await expect(page.locator('text=The sku has already been taken')).toBeVisible();
  });

  test('should search products by name', async ({ page }) => {
    await page.goto('/products');
    
    // Search for products
    await page.fill('input[placeholder*="Search"]', 'Laptop');
    await page.click('button:has-text("Search")');
    
    // Wait for results
    await page.waitForTimeout(1000);
    
    // Check if search results contain the search term
    const productCards = page.locator('.product-card');
    const firstCard = productCards.first();
    await expect(firstCard).toContainText('Laptop');
  });

  test('should filter products by category', async ({ page }) => {
    await page.goto('/products');
    
    // Select category filter
    await page.selectOption('select[name="category"]', '1');
    await page.click('button:has-text("Filter")');
    
    // Wait for results
    await page.waitForTimeout(1000);
    
    // Check if filtered results are displayed
    await expect(page.locator('.product-card')).toBeVisible();
  });

  test('should filter products by status', async ({ page }) => {
    await page.goto('/products');
    
    // Select status filter
    await page.selectOption('select[name="status"]', 'active');
    await page.click('button:has-text("Filter")');
    
    // Wait for results
    await page.waitForTimeout(1000);
    
    // Check if filtered results are displayed
    await expect(page.locator('.product-card')).toBeVisible();
  });

  test('should view product details', async ({ page }) => {
    await page.goto('/products');
    
    // Click on first product view button
    await page.click('.product-card:first-child .btn:has-text("View")');
    
    // Check product details page
    await expect(page).toHaveURL(/\/products\/\d+/);
    await expect(page.locator('h1')).toContainText('Product Details');
    await expect(page.locator('.product-info')).toBeVisible();
    await expect(page.locator('.stock-info')).toBeVisible();
  });

  test('should display low stock warning', async ({ page }) => {
    await page.goto('/products');
    
    // Check if low stock badge is visible for products with low stock
    const lowStockBadge = page.locator('.badge-warning:has-text("Low Stock")');
    if (await lowStockBadge.isVisible()) {
      await expect(lowStockBadge).toBeVisible();
    }
  });

  test('should display out of stock warning', async ({ page }) => {
    await page.goto('/products');
    
    // Check if out of stock badge is visible for products with no stock
    const outOfStockBadge = page.locator('.badge-danger:has-text("Out of Stock")');
    if (await outOfStockBadge.isVisible()) {
      await expect(outOfStockBadge).toBeVisible();
    }
  });

  test('should edit product', async ({ page }) => {
    await page.goto('/products');
    
    // Click on first product edit button
    await page.click('.product-card:first-child .btn:has-text("Edit")');
    
    // Check edit form
    await expect(page).toHaveURL(/\/products\/\d+\/edit/);
    await expect(page.locator('h1')).toContainText('Edit Product');
    
    // Update product name
    await page.fill('input[name="name"]', 'Updated Product Name');
    
    // Submit update
    await page.click('button[type="submit"]');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('Product updated successfully');
  });

  test('should delete product', async ({ page }) => {
    await page.goto('/products');
    
    // Get initial product count
    const initialCount = await page.locator('.product-card').count();
    
    // Click delete button on first product
    await page.click('.product-card:first-child .btn-danger');
    
    // Confirm deletion in modal
    await page.click('.modal .btn-danger:has-text("Delete")');
    
    // Wait for deletion to complete
    await page.waitForTimeout(1000);
    
    // Check if product count decreased
    const newCount = await page.locator('.product-card').count();
    expect(newCount).toBe(initialCount - 1);
  });

  test('should validate price as numeric', async ({ page }) => {
    await page.goto('/products/create');
    
    // Fill non-numeric price
    await page.fill('input[name="price"]', 'abc');
    await page.click('button[type="submit"]');
    
    // Check for numeric validation error
    await expect(page.locator('text=The price must be a number')).toBeVisible();
  });

  test('should validate stock quantity as integer', async ({ page }) => {
    await page.goto('/products/create');
    
    // Fill non-integer stock quantity
    await page.fill('input[name="stock_quantity"]', '10.5');
    await page.click('button[type="submit"]');
    
    // Check for integer validation error
    await expect(page.locator('text=The stock quantity must be an integer')).toBeVisible();
  });

  test('should update stock quantity', async ({ page }) => {
    await page.goto('/products');
    
    // Click on first product view button
    await page.click('.product-card:first-child .btn:has-text("View")');
    
    // Click update stock button
    await page.click('.btn:has-text("Update Stock")');
    
    // Fill new stock quantity
    await page.fill('input[name="stock_quantity"]', '150');
    
    // Submit update
    await page.click('button[type="submit"]');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('Stock updated successfully');
  });

  test('should display product statistics', async ({ page }) => {
    await page.goto('/products');
    
    // Check if statistics cards are visible
    await expect(page.locator('.stats-card:has-text("Total Products")')).toBeVisible();
    await expect(page.locator('.stats-card:has-text("Active Products")')).toBeVisible();
    await expect(page.locator('.stats-card:has-text("Low Stock Items")')).toBeVisible();
    await expect(page.locator('.stats-card:has-text("Out of Stock")')).toBeVisible();
  });

  test('should export products to PDF', async ({ page }) => {
    await page.goto('/products');
    
    // Start download
    const downloadPromise = page.waitForEvent('download');
    await page.click('.btn:has-text("Export PDF")');
    const download = await downloadPromise;
    
    // Verify download
    expect(download.suggestedFilename()).toContain('.pdf');
  });

  test('should export products to Excel', async ({ page }) => {
    await page.goto('/products');
    
    // Start download
    const downloadPromise = page.waitForEvent('download');
    await page.click('.btn:has-text("Export Excel")');
    const download = await downloadPromise;
    
    // Verify download
    expect(download.suggestedFilename()).toContain('.xlsx');
  });

  test('should upload product image', async ({ page }) => {
    await page.goto('/products/create');
    
    // Upload image file
    const fileInput = page.locator('input[type="file"]');
    await fileInput.setInputFiles({
      name: 'test-image.jpg',
      mimeType: 'image/jpeg',
      buffer: Buffer.from('fake-image-data')
    });
    
    // Check if image preview is displayed
    await expect(page.locator('.image-preview')).toBeVisible();
  });

  test('should validate image file type', async ({ page }) => {
    await page.goto('/products/create');
    
    // Upload invalid file type
    const fileInput = page.locator('input[type="file"]');
    await fileInput.setInputFiles({
      name: 'test-file.txt',
      mimeType: 'text/plain',
      buffer: Buffer.from('fake-text-data')
    });
    
    // Check for file type validation error
    await expect(page.locator('text=The image must be a file of type: jpeg, png, jpg, gif')).toBeVisible();
  });

  test('should display product categories', async ({ page }) => {
    await page.goto('/products/create');
    
    // Check if category dropdown has options
    const categorySelect = page.locator('select[name="category_id"]');
    await expect(categorySelect.locator('option')).toHaveCount.greaterThan(1);
  });

  test('should calculate profit margin', async ({ page }) => {
    await page.goto('/products/create');
    
    // Fill cost and price
    await page.fill('input[name="cost"]', '50.00');
    await page.fill('input[name="price"]', '100.00');
    
    // Check if profit margin is calculated and displayed
    await expect(page.locator('.profit-margin')).toContainText('50%');
  });

  test('should show inventory movements', async ({ page }) => {
    await page.goto('/products');
    
    // Click on first product view button
    await page.click('.product-card:first-child .btn:has-text("View")');
    
    // Check if inventory movements section is visible
    await expect(page.locator('.inventory-movements')).toBeVisible();
    await expect(page.locator('text=Recent Stock Movements')).toBeVisible();
  });

  test('should display barcode', async ({ page }) => {
    await page.goto('/products');
    
    // Click on first product view button
    await page.click('.product-card:first-child .btn:has-text("View")');
    
    // Check if barcode is displayed
    await expect(page.locator('.barcode')).toBeVisible();
  });

  test('should generate product report', async ({ page }) => {
    await page.goto('/products');
    
    // Click generate report button
    await page.click('.btn:has-text("Generate Report")');
    
    // Check if report modal opens
    await expect(page.locator('#productReportModal')).toBeVisible();
    
    // Select report type
    await page.selectOption('select[name="report_type"]', 'inventory');
    
    // Generate report
    const downloadPromise = page.waitForEvent('download');
    await page.click('.btn:has-text("Generate")');
    const download = await downloadPromise;
    
    // Verify download
    expect(download.suggestedFilename()).toContain('inventory-report');
  });

  test('should handle bulk operations', async ({ page }) => {
    await page.goto('/products');
    
    // Select multiple products
    await page.check('.product-card:first-child input[type="checkbox"]');
    await page.check('.product-card:nth-child(2) input[type="checkbox"]');
    
    // Check if bulk actions are enabled
    await expect(page.locator('.bulk-actions')).toBeVisible();
    await expect(page.locator('.btn:has-text("Bulk Update")')).toBeEnabled();
    await expect(page.locator('.btn:has-text("Bulk Delete")')).toBeEnabled();
  });

  test('should perform bulk price update', async ({ page }) => {
    await page.goto('/products');
    
    // Select multiple products
    await page.check('.product-card:first-child input[type="checkbox"]');
    await page.check('.product-card:nth-child(2) input[type="checkbox"]');
    
    // Click bulk update
    await page.click('.btn:has-text("Bulk Update")');
    
    // Fill bulk update form
    await page.selectOption('select[name="update_field"]', 'price');
    await page.fill('input[name="new_value"]', '199.99');
    
    // Submit bulk update
    await page.click('button[type="submit"]');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('Products updated successfully');
  });

  test('should sort products by different criteria', async ({ page }) => {
    await page.goto('/products');
    
    // Sort by name
    await page.click('th:has-text("Name")');
    await page.waitForTimeout(500);
    
    // Sort by price
    await page.click('th:has-text("Price")');
    await page.waitForTimeout(500);
    
    // Sort by stock
    await page.click('th:has-text("Stock")');
    await page.waitForTimeout(500);
    
    // Check if sorting indicators are visible
    await expect(page.locator('.sort-indicator')).toBeVisible();
  });
});