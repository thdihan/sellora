import { test, expect } from '@playwright/test';
import { AuthHelper } from './utils/auth.js';

test.describe('Authentication Module', () => {
  let authHelper;

  test.beforeEach(async ({ page }) => {
    authHelper = new AuthHelper(page);
  });

  test('should display login page correctly', async ({ page }) => {
    await page.goto('/login');
    
    // Check page title and form elements
    await expect(page).toHaveTitle(/Login/);
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();
    await expect(page.locator('text=Remember Me')).toBeVisible();
  });

  test('should show validation errors for empty fields', async ({ page }) => {
    await page.goto('/login');
    
    // Try to submit empty form
    await page.click('button[type="submit"]');
    
    // Check for validation messages
    await expect(page.locator('text=The email field is required')).toBeVisible();
    await expect(page.locator('text=The password field is required')).toBeVisible();
  });

  test('should show error for invalid credentials', async ({ page }) => {
    await page.goto('/login');
    
    await page.fill('input[name="email"]', 'invalid@test.com');
    await page.fill('input[name="password"]', 'wrongpassword');
    await page.click('button[type="submit"]');
    
    // Check for error message
    await expect(page.locator('text=These credentials do not match our records')).toBeVisible();
  });

  test('should login successfully as admin', async ({ page }) => {
    await authHelper.loginAsAdmin();
    
    // Verify successful login
    await expect(page).toHaveURL('/dashboard');
    await expect(page.locator('h1')).toContainText('Dashboard');
    await expect(page.locator('[data-bs-toggle="dropdown"]')).toBeVisible();
  });

  test('should login successfully as manager', async ({ page }) => {
    await authHelper.loginAsManager();
    
    // Verify successful login
    await expect(page).toHaveURL('/dashboard');
    await expect(page.locator('h1')).toContainText('Dashboard');
    await expect(page.locator('[data-bs-toggle="dropdown"]')).toBeVisible();
  });

  test('should login successfully as employee', async ({ page }) => {
    await authHelper.loginAsEmployee();
    
    // Verify successful login
    await expect(page).toHaveURL('/dashboard');
    await expect(page.locator('h1')).toContainText('Dashboard');
    await expect(page.locator('[data-bs-toggle="dropdown"]')).toBeVisible();
  });

  test('should logout successfully', async ({ page }) => {
    await authHelper.loginAsAdmin();
    
    // Logout
    await authHelper.logout();
    
    // Verify logout
    await expect(page).toHaveURL('/login');
    await expect(page.locator('input[name="email"]')).toBeVisible();
  });

  test('should redirect to login when accessing protected route', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Should redirect to login
    await expect(page).toHaveURL('/login');
  });

  test('should remember login state', async ({ page }) => {
    await page.goto('/login');
    
    // Login with remember me checked
    await page.fill('input[name="email"]', 'admin.demo@demo.local');
    await page.fill('input[name="password"]', 'AdminDemo123!');
    await page.check('input[name="remember"]');
    await page.click('button[type="submit"]');
    
    await page.waitForURL('/dashboard');
    
    // Reload page and verify still logged in
    await page.reload();
    await expect(page).toHaveURL('/dashboard');
    await expect(page.locator('[data-bs-toggle="dropdown"]')).toBeVisible();
  });

  test('should handle session timeout gracefully', async ({ page }) => {
    await authHelper.loginAsAdmin();
    
    // Simulate session expiry by clearing cookies
    await page.context().clearCookies();
    
    // Try to access protected route
    await page.goto('/orders');
    
    // Should redirect to login
    await expect(page).toHaveURL('/login');
  });

  test('should validate email format', async ({ page }) => {
    await page.goto('/login');
    
    await page.fill('input[name="email"]', 'invalid-email');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    
    // Check for email validation error
    await expect(page.locator('text=The email must be a valid email address')).toBeVisible();
  });

  test('should show password requirements', async ({ page }) => {
    await page.goto('/login');
    
    await page.fill('input[name="email"]', 'test@test.com');
    await page.fill('input[name="password"]', '123');
    await page.click('button[type="submit"]');
    
    // Check for password length validation
    await expect(page.locator('text=The password must be at least 8 characters')).toBeVisible();
  });
});