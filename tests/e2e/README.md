# Sellora E2E Testing with Playwright

This directory contains comprehensive end-to-end tests for the Sellora application using Playwright.

## Test Coverage

The e2e test suite covers all major modules of the Sellora application:

### 🔐 Authentication (`auth.spec.js`)
- Login/logout functionality
- Form validation
- Role-based access
- Session management
- Password requirements
- Remember me functionality

### 📦 Orders (`orders.spec.js`)
- Order creation and management
- Form validation and calculations
- Discount application
- Search and filtering
- Order status updates
- Export functionality

### 👥 Customers (`customers.spec.js`)
- Customer management (CRUD operations)
- Customer analytics modal
- Due calculation service integration
- Search and filtering
- Export functionality
- Credit limit management

### 🛍️ Products (`products.spec.js`)
- Product management (CRUD operations)
- Inventory tracking
- Stock level monitoring
- Category management
- Bulk operations
- Image upload
- Barcode generation

### 🧾 Bills (`bills.spec.js`)
- Bill creation and management
- Attachment handling
- Search and filtering
- Status management
- Export functionality

### 📊 Dashboard (`dashboard.spec.js`)
- KPI display
- Charts and analytics
- Date range filtering
- Quick actions
- Real-time updates
- Mobile responsiveness

### ⚙️ Settings (`settings.spec.js`)
- General application settings
- User management
- Email configuration
- Security settings
- Backup management
- System information

## Prerequisites

1. **Node.js** (v16 or higher)
2. **Laravel application** running on `http://127.0.0.1:8000`
3. **Database** with test data seeded
4. **Playwright browsers** installed

## Installation

Playwright is already installed as a dev dependency. If you need to reinstall:

```bash
npm install --save-dev @playwright/test
npx playwright install
```

## Running Tests

### Run All Tests
```bash
# Headless mode (default)
npm run test:e2e

# With browser UI visible
npm run test:e2e:headed

# Interactive UI mode
npm run test:e2e:ui
```

### Run Specific Module Tests
```bash
npm run test:auth        # Authentication tests
npm run test:orders      # Orders module tests
npm run test:customers   # Customers module tests
npm run test:products    # Products module tests
npm run test:bills       # Bills module tests
npm run test:dashboard   # Dashboard tests
npm run test:settings    # Settings tests
```

### View Test Reports
```bash
npm run test:e2e:report
```

## Test Configuration

The test configuration is defined in `playwright.config.js`:

- **Base URL**: `http://127.0.0.1:8000`
- **Browsers**: Chromium, Firefox, WebKit
- **Parallel execution**: Enabled
- **Retries**: 2 on CI, 0 locally
- **Timeout**: 30 seconds per test
- **Screenshots**: On failure
- **Video**: On first retry

## Test Structure

Each test file follows a consistent structure:

```javascript
import { test, expect } from '@playwright/test';
import { AuthHelper, TestDataHelper } from './utils/auth.js';

test.describe('Module Name', () => {
  let authHelper;

  test.beforeEach(async ({ page }) => {
    authHelper = new AuthHelper(page);
    await authHelper.loginAsAdmin();
  });

  test('should perform specific action', async ({ page }) => {
    // Test implementation
  });
});
```

## Utilities

### AuthHelper
Provides authentication methods:
- `loginAsAdmin()` - Login as administrator
- `loginAsManager()` - Login as manager
- `loginAsEmployee()` - Login as employee
- `logout()` - Logout current user
- `isLoggedIn()` - Check login status

### TestDataHelper
Generates test data:
- `generateCustomer()` - Random customer data
- `generateProduct()` - Random product data
- `generateOrder()` - Random order data
- `generateBill()` - Random bill data

## Best Practices

### 1. Test Independence
- Each test should be independent and not rely on other tests
- Use `test.beforeEach()` for setup
- Clean up after tests if necessary

### 2. Reliable Selectors
- Use data attributes when possible: `[data-testid="submit-btn"]`
- Prefer text content: `text=Submit`
- Avoid CSS selectors that might change

### 3. Waiting Strategies
- Use `await expect()` for assertions
- Use `page.waitForTimeout()` sparingly
- Prefer `page.waitForSelector()` or `page.waitForResponse()`

### 4. Error Handling
- Test both success and error scenarios
- Verify error messages are displayed
- Test form validation

## Debugging Tests

### Run in Debug Mode
```bash
npx playwright test --debug
```

### Run Specific Test
```bash
npx playwright test tests/e2e/auth.spec.js --debug
```

### View Trace
```bash
npx playwright show-trace trace.zip
```

## CI/CD Integration

For continuous integration, add this to your workflow:

```yaml
- name: Install dependencies
  run: npm ci

- name: Install Playwright browsers
  run: npx playwright install --with-deps

- name: Run Playwright tests
  run: npm run test:e2e

- name: Upload test results
  uses: actions/upload-artifact@v3
  if: always()
  with:
    name: playwright-report
    path: playwright-report/
```

## Maintenance

### Adding New Tests
1. Create test file in appropriate module
2. Follow existing naming conventions
3. Use helper utilities for common operations
4. Add test script to `package.json` if needed

### Updating Tests
- Keep tests updated with UI changes
- Update selectors when elements change
- Maintain test data generators
- Review and update assertions

### Performance
- Monitor test execution time
- Optimize slow tests
- Use parallel execution effectively
- Consider test sharding for large suites

## Troubleshooting

### Common Issues

1. **Tests failing due to timing**
   - Increase timeout in config
   - Use proper waiting strategies
   - Check for race conditions

2. **Element not found**
   - Verify selector accuracy
   - Check if element is visible
   - Wait for element to appear

3. **Authentication issues**
   - Verify test user credentials
   - Check session management
   - Ensure proper logout between tests

4. **Database state**
   - Ensure test database is seeded
   - Check for data dependencies
   - Consider database reset between runs

## Contributing

When contributing to the test suite:

1. Follow existing patterns and conventions
2. Write descriptive test names
3. Add comments for complex test logic
4. Update this README for significant changes
5. Ensure tests pass before submitting

## Support

For issues with the test suite:
1. Check this README first
2. Review Playwright documentation
3. Check existing issues in the project
4. Create detailed bug reports with screenshots/traces