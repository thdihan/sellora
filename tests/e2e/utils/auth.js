import { expect } from '@playwright/test';

/**
 * Authentication utilities for Playwright tests
 */
export class AuthHelper {
  constructor(page) {
    this.page = page;
  }

  /**
   * Login with admin credentials
   */
  async loginAsAdmin() {
    await this.page.goto('/login');
    await this.page.fill('input[name="email"]', 'admin.demo@demo.local');
    await this.page.fill('input[name="password"]', 'AdminDemo123!');
    await this.page.click('button[type="submit"]');
    
    // Wait for redirect to dashboard
    await this.page.waitForURL('/dashboard');
    await expect(this.page.locator('h1')).toContainText('Dashboard');
  }

  /**
   * Login with manager credentials
   */
  async loginAsManager() {
    await this.page.goto('/login');
    await this.page.fill('input[name="email"]', 'manager.demo@demo.local');
    await this.page.fill('input[name="password"]', 'ManagerDemo123!');
    await this.page.click('button[type="submit"]');
    
    // Wait for redirect to dashboard
    await this.page.waitForURL('/dashboard');
    await expect(this.page.locator('h1')).toContainText('Dashboard');
  }

  /**
   * Login with employee credentials
   */
  async loginAsEmployee() {
    await this.page.goto('/login');
    await this.page.fill('input[name="email"]', 'employee.demo@demo.local');
    await this.page.fill('input[name="password"]', 'EmployeeDemo123!');
    await this.page.click('button[type="submit"]');
    
    // Wait for redirect to dashboard
    await this.page.waitForURL('/dashboard');
    await expect(this.page.locator('h1')).toContainText('Dashboard');
  }

  /**
   * Logout current user
   */
  async logout() {
    // Click on user dropdown
    await this.page.click('[data-bs-toggle="dropdown"]');
    await this.page.click('text=Logout');
    
    // Wait for redirect to login
    await this.page.waitForURL('/login');
  }

  /**
   * Check if user is logged in
   */
  async isLoggedIn() {
    try {
      await this.page.waitForSelector('[data-bs-toggle="dropdown"]', { timeout: 5000 });
      return true;
    } catch {
      return false;
    }
  }
}

/**
 * Common test data generators
 */
export class TestDataHelper {
  static generateCustomer() {
    const timestamp = Date.now();
    return {
      name: `Test Customer ${timestamp}`,
      email: `customer${timestamp}@test.com`,
      phone: `+1234567${timestamp.toString().slice(-3)}`,
      address: `${timestamp} Test Street, Test City, TC 12345`
    };
  }

  static generateProduct() {
    const timestamp = Date.now();
    return {
      name: `Test Product ${timestamp}`,
      description: `Test product description ${timestamp}`,
      price: Math.floor(Math.random() * 1000) + 10,
      category: 'Electronics'
    };
  }

  static generateOrder() {
    const timestamp = Date.now();
    return {
      customer_name: `Order Customer ${timestamp}`,
      customer_email: `order${timestamp}@test.com`,
      customer_phone: `+1234567${timestamp.toString().slice(-3)}`,
      customer_address: `${timestamp} Order Street, Order City, OC 12345`,
      product_name: `Order Product ${timestamp}`,
      quantity: Math.floor(Math.random() * 10) + 1,
      unit_price: Math.floor(Math.random() * 100) + 10
    };
  }

  static generateBill() {
    const timestamp = Date.now();
    return {
      amount: Math.floor(Math.random() * 1000) + 100,
      description: `Test bill ${timestamp}`,
      category: 'Office Supplies',
      priority: 'medium'
    };
  }
}