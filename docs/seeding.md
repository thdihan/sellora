# Demo Data Seeding Guide

This guide explains how to use the demo data seeding system to populate your application with realistic test data and role-based demo users.

## Overview

The demo seeding system provides:

- **Role-based demo users** for each role in the system
- **Secure owner bootstrap** with environment-based configuration
- **Idempotent seeding** that can be run multiple times safely
- **Module demo data** for all application features
- **Environment protection** to prevent accidental production seeding
- **Owner account protection** with deletion guards

## Quick Start

### 1. Set Environment Variables

Add these variables to your `.env` file:

```bash
# Required: Owner bootstrap configuration
BOOTSTRAP_OWNER_EMAIL=your-email@example.com
BOOTSTRAP_OWNER_PASSWORD=YourSecurePassword123!

# Optional: Demo seeding configuration
ALLOW_DEMO_SEED_IN_PROD=false
INCLUDE_OWNER_PASSWORD_IN_OUTPUT=false
ALLOW_OWNER_MUTATION=false
```

### 2. Run Demo Seeding

```bash
# Basic demo seeding
php artisan demo:seed

# With verification
php artisan demo:seed --verify

# Force in production (not recommended)
php artisan demo:seed --force

# Set owner password via command line
php artisan demo:seed --owner-password="SecurePassword123!"
```

### 3. Check Results

After seeding, check the generated `DEMO_CREDENTIALS.md` file for login details.

## Environment Variables

### Required Variables

| Variable | Description | Example |
|----------|-------------|----------|
| `BOOTSTRAP_OWNER_EMAIL` | Email for the system owner account | `admin@yourcompany.com` |
| `BOOTSTRAP_OWNER_PASSWORD` | Password for the owner account | `SecurePassword123!` |

### Optional Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `ALLOW_DEMO_SEED_IN_PROD` | `false` | Allow seeding in production environment |
| `INCLUDE_OWNER_PASSWORD_IN_OUTPUT` | `false` | Include owner password in credentials file |
| `ALLOW_OWNER_MUTATION` | `false` | Allow owner account deletion/role changes |

## Demo Users

The system creates one demo user for each role with the following pattern:

- **Email format**: `{role}.demo@demo.local`
- **Password**: Randomly generated strong password (16 characters)
- **Examples**:
  - `admin.demo@demo.local`
  - `nsm.demo@demo.local`
  - `mr.demo@demo.local`

### Available Roles

Based on your role configuration, demo users are created for:

- Admin
- Chairman
- Director
- ED
- GM
- DGM
- AGM
- NSM
- ZSM
- RSM
- ASM
- MPO
- MR
- Trainee

*Note: The `Author` role is reserved for the owner account.*

## Owner Account Security

### Bootstrap Process

1. Owner account is created using environment variables
2. Assigned the `Author` role (highest privileges)
3. Protected from deletion and role changes
4. Password must be provided via environment or command prompt

### Protection Features

- **Deletion Guard**: Owner cannot be deleted without `ALLOW_OWNER_MUTATION=true`
- **Role Protection**: Owner role cannot be changed without override
- **Audit Logging**: All mutation attempts are logged with IP and user agent
- **No Backdoors**: No hardcoded credentials or universal passwords

### Break-Glass Access

To modify the owner account in emergencies:

1. Set `ALLOW_OWNER_MUTATION=true` in environment
2. Perform the required operation
3. Immediately set `ALLOW_OWNER_MUTATION=false`
4. Review audit logs

## Module Demo Data

The seeder creates realistic demo data for:

- **Settings**: Footer branding configuration
- **Orders**: Sample order data (planned)
- **Bills**: Sample billing data (planned)
- **Budgets**: Sample budget data (planned)
- **Assessments**: Sample assessment data (planned)
- **Locations**: Sample location data (planned)
- **Reports**: Sample report data (planned)

*Note: Additional module seeders will be added as features are developed.*

## Scripts and Commands

### Artisan Commands

```bash
# Main seeding command
php artisan demo:seed

# With options
php artisan demo:seed --force --verify --owner-password="password"

# Run specific seeders
php artisan db:seed --class=DemoSeeder
php artisan db:seed --class=RoleSeeder
```

### Testing

```bash
# Run demo seeding tests
php artisan test --filter=DemoSeedingTest

# Run all tests
php artisan test
```

## File Structure

```
database/
├── seeders/
│   ├── DemoSeeder.php          # Main demo seeder
│   ├── RoleSeeder.php          # Role definitions
│   └── FooterBrandSeeder.php   # Settings demo data
├── factories/
│   └── UserFactory.php         # Enhanced user factory
app/
├── Console/Commands/
│   └── SeedDemoData.php        # CLI command
├── Models/
│   └── User.php                # With owner protection
tests/
├── Feature/
│   └── DemoSeedingTest.php     # Seeding tests
docs/
└── seeding.md                  # This documentation
```

## Security Best Practices

### Environment Configuration

1. **Never commit secrets**: Use `.env` files and environment variables
2. **Strong passwords**: Use complex passwords for owner account
3. **Production protection**: Keep `ALLOW_DEMO_SEED_IN_PROD=false` in production
4. **Owner mutation**: Keep `ALLOW_OWNER_MUTATION=false` unless needed

### Access Control

1. **Role-based access**: Each demo user has appropriate role permissions
2. **Owner protection**: System owner cannot be deleted accidentally
3. **Audit logging**: All security events are logged
4. **No backdoors**: No hardcoded or universal credentials

### Data Management

1. **Idempotent seeding**: Safe to run multiple times
2. **Transaction safety**: All seeding operations use database transactions
3. **Cleanup**: Demo data can be safely removed and regenerated
4. **Verification**: Built-in verification ensures data integrity

## Troubleshooting

### Common Issues

**Error: "Owner password is required"**
- Solution: Set `BOOTSTRAP_OWNER_PASSWORD` in `.env` or use `--owner-password` flag

**Error: "Cannot run demo seeding in production"**
- Solution: Use `--force` flag or set `ALLOW_DEMO_SEED_IN_PROD=true`

**Error: "Author role not found"**
- Solution: Run `php artisan db:seed --class=RoleSeeder` first

**Error: "Owner account cannot be deleted"**
- Solution: This is expected behavior. Set `ALLOW_OWNER_MUTATION=true` if needed

### Verification

```bash
# Check if seeding worked
php artisan demo:seed --verify

# Manual verification
php artisan tinker
>>> App\Models\User::where('email', 'like', '%.demo@demo.local')->count()
>>> App\Models\User::where('email', env('BOOTSTRAP_OWNER_EMAIL'))->first()
```

### Logs

Check application logs for seeding and security events:

```bash
tail -f storage/logs/laravel.log
```

## Advanced Usage

### Custom Module Seeders

To add demo data for new modules:

1. Create a new seeder class
2. Add it to `DemoSeeder::seedModuleData()`
3. Ensure idempotent operations
4. Add verification tests

### Environment-Specific Configuration

```bash
# Development
ALLOW_DEMO_SEED_IN_PROD=false
INCLUDE_OWNER_PASSWORD_IN_OUTPUT=true

# Staging
ALLOW_DEMO_SEED_IN_PROD=true
INCLUDE_OWNER_PASSWORD_IN_OUTPUT=false

# Production
ALLOW_DEMO_SEED_IN_PROD=false
ALLOW_OWNER_MUTATION=false
```

### CI/CD Integration

```yaml
# Example GitHub Actions
- name: Seed Demo Data
  run: |
    php artisan demo:seed --force --verify
  env:
    BOOTSTRAP_OWNER_PASSWORD: ${{ secrets.DEMO_OWNER_PASSWORD }}
```

## Support

For issues or questions:

1. Check this documentation
2. Review the troubleshooting section
3. Check application logs
4. Run verification tests
5. Contact the platform engineering team