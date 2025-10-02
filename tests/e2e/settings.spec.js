import { test, expect } from '@playwright/test';
import { AuthHelper } from './utils/auth.js';

test.describe('Settings Module', () => {
  let authHelper;

  test.beforeEach(async ({ page }) => {
    authHelper = new AuthHelper(page);
    await authHelper.loginAsAdmin();
  });

  test('should display settings page correctly', async ({ page }) => {
    await page.goto('/settings');
    
    // Check page elements
    await expect(page.locator('h1')).toContainText('Settings');
    await expect(page.locator('.settings-nav')).toBeVisible();
    await expect(page.locator('.settings-content')).toBeVisible();
  });

  test('should display general settings tab', async ({ page }) => {
    await page.goto('/settings');
    
    // Click general settings tab
    await page.click('.settings-nav a:has-text("General")');
    
    // Check general settings form
    await expect(page.locator('input[name="app_name"]')).toBeVisible();
    await expect(page.locator('input[name="app_url"]')).toBeVisible();
    await expect(page.locator('textarea[name="app_description"]')).toBeVisible();
    await expect(page.locator('input[name="contact_email"]')).toBeVisible();
    await expect(page.locator('input[name="contact_phone"]')).toBeVisible();
  });

  test('should update general settings', async ({ page }) => {
    await page.goto('/settings');
    
    // Click general settings tab
    await page.click('.settings-nav a:has-text("General")');
    
    // Update app name
    await page.fill('input[name="app_name"]', 'Updated Sellora');
    
    // Update contact email
    await page.fill('input[name="contact_email"]', 'updated@sellora.com');
    
    // Save settings
    await page.click('button[type="submit"]');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('Settings updated successfully');
  });

  test('should display user management tab', async ({ page }) => {
    await page.goto('/settings');
    
    // Click user management tab
    await page.click('.settings-nav a:has-text("Users")');
    
    // Check user management elements
    await expect(page.locator('text=User Management')).toBeVisible();
    await expect(page.locator('.btn:has-text("Add New User")')).toBeVisible();
    await expect(page.locator('.users-table')).toBeVisible();
  });

  test('should create new user', async ({ page }) => {
    await page.goto('/settings');
    
    // Click user management tab
    await page.click('.settings-nav a:has-text("Users")');
    
    // Click add new user button
    await page.click('.btn:has-text("Add New User")');
    
    // Fill user form
    await page.fill('input[name="name"]', 'Test User');
    await page.fill('input[name="email"]', 'testuser@example.com');
    await page.fill('input[name="password"]', 'password123');
    await page.fill('input[name="password_confirmation"]', 'password123');
    await page.selectOption('select[name="role"]', 'employee');
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('User created successfully');
  });

  test('should edit user', async ({ page }) => {
    await page.goto('/settings');
    
    // Click user management tab
    await page.click('.settings-nav a:has-text("Users")');
    
    // Click edit button on first user
    await page.click('.users-table tbody tr:first-child .btn:has-text("Edit")');
    
    // Update user name
    await page.fill('input[name="name"]', 'Updated User Name');
    
    // Submit update
    await page.click('button[type="submit"]');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('User updated successfully');
  });

  test('should delete user', async ({ page }) => {
    await page.goto('/settings');
    
    // Click user management tab
    await page.click('.settings-nav a:has-text("Users")');
    
    // Get initial user count
    const initialCount = await page.locator('.users-table tbody tr').count();
    
    // Click delete button on last user
    await page.click('.users-table tbody tr:last-child .btn-danger');
    
    // Confirm deletion
    await page.click('.modal .btn-danger:has-text("Delete")');
    
    // Wait for deletion to complete
    await page.waitForTimeout(1000);
    
    // Check if user count decreased
    const newCount = await page.locator('.users-table tbody tr').count();
    expect(newCount).toBe(initialCount - 1);
  });

  test('should display email settings tab', async ({ page }) => {
    await page.goto('/settings');
    
    // Click email settings tab
    await page.click('.settings-nav a:has-text("Email")');
    
    // Check email settings form
    await expect(page.locator('input[name="mail_host"]')).toBeVisible();
    await expect(page.locator('input[name="mail_port"]')).toBeVisible();
    await expect(page.locator('input[name="mail_username"]')).toBeVisible();
    await expect(page.locator('input[name="mail_password"]')).toBeVisible();
    await expect(page.locator('select[name="mail_encryption"]')).toBeVisible();
  });

  test('should update email settings', async ({ page }) => {
    await page.goto('/settings');
    
    // Click email settings tab
    await page.click('.settings-nav a:has-text("Email")');
    
    // Update email settings
    await page.fill('input[name="mail_host"]', 'smtp.gmail.com');
    await page.fill('input[name="mail_port"]', '587');
    await page.fill('input[name="mail_username"]', 'test@gmail.com');
    await page.selectOption('select[name="mail_encryption"]', 'tls');
    
    // Save settings
    await page.click('button[type="submit"]');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('Email settings updated successfully');
  });

  test('should test email configuration', async ({ page }) => {
    await page.goto('/settings');
    
    // Click email settings tab
    await page.click('.settings-nav a:has-text("Email")');
    
    // Click test email button
    await page.click('.btn:has-text("Test Email")');
    
    // Fill test email address
    await page.fill('input[name="test_email"]', 'test@example.com');
    
    // Send test email
    await page.click('.btn:has-text("Send Test")');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('Test email sent successfully');
  });

  test('should display backup settings tab', async ({ page }) => {
    await page.goto('/settings');
    
    // Click backup settings tab
    await page.click('.settings-nav a:has-text("Backup")');
    
    // Check backup settings elements
    await expect(page.locator('text=Database Backup')).toBeVisible();
    await expect(page.locator('.btn:has-text("Create Backup")')).toBeVisible();
    await expect(page.locator('.backup-list')).toBeVisible();
  });

  test('should create database backup', async ({ page }) => {
    await page.goto('/settings');
    
    // Click backup settings tab
    await page.click('.settings-nav a:has-text("Backup")');
    
    // Click create backup button
    await page.click('.btn:has-text("Create Backup")');
    
    // Wait for backup to complete
    await page.waitForTimeout(5000);
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('Backup created successfully');
  });

  test('should download backup file', async ({ page }) => {
    await page.goto('/settings');
    
    // Click backup settings tab
    await page.click('.settings-nav a:has-text("Backup")');
    
    // Start download
    const downloadPromise = page.waitForEvent('download');
    await page.click('.backup-list .btn:has-text("Download")');
    const download = await downloadPromise;
    
    // Verify download
    expect(download.suggestedFilename()).toContain('.sql');
  });

  test('should display security settings tab', async ({ page }) => {
    await page.goto('/settings');
    
    // Click security settings tab
    await page.click('.settings-nav a:has-text("Security")');
    
    // Check security settings form
    await expect(page.locator('input[name="session_timeout"]')).toBeVisible();
    await expect(page.locator('input[name="max_login_attempts"]')).toBeVisible();
    await expect(page.locator('input[name="lockout_duration"]')).toBeVisible();
    await expect(page.locator('input[type="checkbox"][name="two_factor_auth"]')).toBeVisible();
  });

  test('should update security settings', async ({ page }) => {
    await page.goto('/settings');
    
    // Click security settings tab
    await page.click('.settings-nav a:has-text("Security")');
    
    // Update security settings
    await page.fill('input[name="session_timeout"]', '30');
    await page.fill('input[name="max_login_attempts"]', '5');
    await page.fill('input[name="lockout_duration"]', '15');
    await page.check('input[type="checkbox"][name="two_factor_auth"]');
    
    // Save settings
    await page.click('button[type="submit"]');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('Security settings updated successfully');
  });

  test('should display notification settings tab', async ({ page }) => {
    await page.goto('/settings');
    
    // Click notification settings tab
    await page.click('.settings-nav a:has-text("Notifications")');
    
    // Check notification settings form
    await expect(page.locator('input[type="checkbox"][name="email_notifications"]')).toBeVisible();
    await expect(page.locator('input[type="checkbox"][name="sms_notifications"]')).toBeVisible();
    await expect(page.locator('input[type="checkbox"][name="low_stock_alerts"]')).toBeVisible();
    await expect(page.locator('input[type="checkbox"][name="order_notifications"]')).toBeVisible();
  });

  test('should update notification settings', async ({ page }) => {
    await page.goto('/settings');
    
    // Click notification settings tab
    await page.click('.settings-nav a:has-text("Notifications")');
    
    // Update notification settings
    await page.check('input[type="checkbox"][name="email_notifications"]');
    await page.check('input[type="checkbox"][name="low_stock_alerts"]');
    await page.uncheck('input[type="checkbox"][name="sms_notifications"]');
    
    // Save settings
    await page.click('button[type="submit"]');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('Notification settings updated successfully');
  });

  test('should display system information tab', async ({ page }) => {
    await page.goto('/settings');
    
    // Click system information tab
    await page.click('.settings-nav a:has-text("System Info")');
    
    // Check system information elements
    await expect(page.locator('text=PHP Version')).toBeVisible();
    await expect(page.locator('text=Laravel Version')).toBeVisible();
    await expect(page.locator('text=Database Version')).toBeVisible();
    await expect(page.locator('text=Server Information')).toBeVisible();
  });

  test('should display logs tab', async ({ page }) => {
    await page.goto('/settings');
    
    // Click logs tab
    await page.click('.settings-nav a:has-text("Logs")');
    
    // Check logs elements
    await expect(page.locator('text=Application Logs')).toBeVisible();
    await expect(page.locator('.log-entries')).toBeVisible();
    await expect(page.locator('.btn:has-text("Clear Logs")')).toBeVisible();
  });

  test('should filter logs by level', async ({ page }) => {
    await page.goto('/settings');
    
    // Click logs tab
    await page.click('.settings-nav a:has-text("Logs")');
    
    // Filter by error level
    await page.selectOption('select[name="log_level"]', 'error');
    await page.click('.btn:has-text("Filter")');
    
    // Wait for results
    await page.waitForTimeout(1000);
    
    // Check if filtered logs are displayed
    await expect(page.locator('.log-entry')).toBeVisible();
  });

  test('should clear application logs', async ({ page }) => {
    await page.goto('/settings');
    
    // Click logs tab
    await page.click('.settings-nav a:has-text("Logs")');
    
    // Click clear logs button
    await page.click('.btn:has-text("Clear Logs")');
    
    // Confirm clearing
    await page.click('.modal .btn-danger:has-text("Clear")');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('Logs cleared successfully');
  });

  test('should display tax settings tab', async ({ page }) => {
    await page.goto('/settings');
    
    // Click tax settings tab
    await page.click('.settings-nav a:has-text("Tax")');
    
    // Check tax settings form
    await expect(page.locator('input[name="default_tax_rate"]')).toBeVisible();
    await expect(page.locator('input[name="tax_name"]')).toBeVisible();
    await expect(page.locator('input[type="checkbox"][name="tax_inclusive"]')).toBeVisible();
  });

  test('should update tax settings', async ({ page }) => {
    await page.goto('/settings');
    
    // Click tax settings tab
    await page.click('.settings-nav a:has-text("Tax")');
    
    // Update tax settings
    await page.fill('input[name="default_tax_rate"]', '15');
    await page.fill('input[name="tax_name"]', 'VAT');
    await page.check('input[type="checkbox"][name="tax_inclusive"]');
    
    // Save settings
    await page.click('button[type="submit"]');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('Tax settings updated successfully');
  });

  test('should display currency settings tab', async ({ page }) => {
    await page.goto('/settings');
    
    // Click currency settings tab
    await page.click('.settings-nav a:has-text("Currency")');
    
    // Check currency settings form
    await expect(page.locator('select[name="default_currency"]')).toBeVisible();
    await expect(page.locator('input[name="currency_symbol"]')).toBeVisible();
    await expect(page.locator('select[name="currency_position"]')).toBeVisible();
  });

  test('should update currency settings', async ({ page }) => {
    await page.goto('/settings');
    
    // Click currency settings tab
    await page.click('.settings-nav a:has-text("Currency")');
    
    // Update currency settings
    await page.selectOption('select[name="default_currency"]', 'USD');
    await page.fill('input[name="currency_symbol"]', '$');
    await page.selectOption('select[name="currency_position"]', 'before');
    
    // Save settings
    await page.click('button[type="submit"]');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('Currency settings updated successfully');
  });

  test('should validate required fields', async ({ page }) => {
    await page.goto('/settings');
    
    // Click general settings tab
    await page.click('.settings-nav a:has-text("General")');
    
    // Clear required field
    await page.fill('input[name="app_name"]', '');
    
    // Try to save
    await page.click('button[type="submit"]');
    
    // Check for validation error
    await expect(page.locator('text=The app name field is required')).toBeVisible();
  });

  test('should validate email format', async ({ page }) => {
    await page.goto('/settings');
    
    // Click general settings tab
    await page.click('.settings-nav a:has-text("General")');
    
    // Fill invalid email
    await page.fill('input[name="contact_email"]', 'invalid-email');
    
    // Try to save
    await page.click('button[type="submit"]');
    
    // Check for validation error
    await expect(page.locator('text=The contact email must be a valid email address')).toBeVisible();
  });

  test('should handle settings access for different roles', async ({ page }) => {
    // Login as employee
    await authHelper.loginAsEmployee();
    
    await page.goto('/settings');
    
    // Check if access is restricted
    await expect(page.locator('text=Access Denied')).toBeVisible();
  });

  test('should export settings configuration', async ({ page }) => {
    await page.goto('/settings');
    
    // Start download
    const downloadPromise = page.waitForEvent('download');
    await page.click('.btn:has-text("Export Settings")');
    const download = await downloadPromise;
    
    // Verify download
    expect(download.suggestedFilename()).toContain('settings');
  });

  test('should import settings configuration', async ({ page }) => {
    await page.goto('/settings');
    
    // Upload settings file
    const fileInput = page.locator('input[type="file"][name="settings_file"]');
    await fileInput.setInputFiles({
      name: 'settings.json',
      mimeType: 'application/json',
      buffer: Buffer.from(JSON.stringify({ app_name: 'Imported Sellora' }))
    });
    
    // Import settings
    await page.click('.btn:has-text("Import Settings")');
    
    // Check for success message
    await expect(page.locator('.alert-success')).toContainText('Settings imported successfully');
  });
});