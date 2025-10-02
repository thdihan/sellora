# Project Overview
- Last scanned: 2025-01-14 15:30:00 UTC

## Modules & Packages
| Module | Path | Key Responsibilities | Main Exports |
|---|---|---|---|
| Authentication | `app/Http/Controllers/Auth/` | User authentication, registration, password management | AuthenticatedSessionController, RegisteredUserController, PasswordResetLinkController |
| Location Tracking | `app/Http/Controllers/LocationTrackingController.php` | Real-time GPS tracking, team mapping | LocationTrackingController, location API endpoints |
| Orders Management | `app/Http/Controllers/OrderController.php` | Order processing, approval workflow | OrderController, order CRUD operations |
| Bills Management | `app/Http/Controllers/BillController.php` | Bill processing, file attachments | BillController, bill CRUD operations |
| Expenses Management | `app/Http/Controllers/ExpenseController.php` | Expense tracking and reporting | ExpenseController, expense CRUD operations |
| Visits Management | `app/Http/Controllers/VisitController.php` | Visit scheduling and tracking | VisitController, visit CRUD operations |
| Events Management | `app/Http/Controllers/EventController.php` | Event scheduling and management | EventController, event CRUD operations |
| Budgets Management | `app/Http/Controllers/BudgetController.php` | Budget planning and tracking | BudgetController, budget CRUD operations |
| Assessments | `app/Http/Controllers/AssessmentController.php` | Assessment creation and evaluation | AssessmentController, assessment CRUD operations |
| Presentations | `app/Http/Controllers/PresentationController.php` | Presentation management and analytics | PresentationController, presentation CRUD operations |
| Reports | `app/Http/Controllers/ReportController.php` | Business reporting and analytics | ReportController, report generation |
| Notifications | `app/Services/Mail/` | Email notifications and queue management | NotificationService, PhpMailerService |
| File Services | `app/Services/` | File upload and management | BillFileService, PresentationFileService |

## Features by Module

### Authentication
- Features: User registration, login, password reset, email verification
- Public API: POST `/login`, POST `/register`, POST `/forgot-password`, POST `/reset-password`
- Key files: `app/Http/Controllers/Auth/AuthenticatedSessionController.php` (L1-L50), `routes/auth.php` (L1-L30)

### Location Tracking
- Features: Real-time GPS tracking, team mapping, role-based access control, offline PWA support
- Public API: POST `/api/locations`, GET `/api/locations/latest`, GET `/api/locations/history`
- Key files: `app/Http/Controllers/LocationTrackingController.php` (L1-L100), `README-LOCATION-TRACKING.md` (L1-L278)

### Orders Management
- Features: Order creation, approval workflow, file attachments, notifications
- Public API: Resource routes `/orders/*`, POST `/orders/{id}/approve`
- Key files: `app/Http/Controllers/OrderController.php` (L1-L200), `routes/web.php` (L25-L30)

### Bills Management
- Features: Bill processing, file uploads, approval workflow
- Public API: Resource routes `/bills/*`
- Key files: `app/Http/Controllers/BillController.php` (L1-L150), `app/Services/BillFileService.php` (L1-L100)

### Expenses Management
- Features: Expense tracking, categorization, reporting
- Public API: Resource routes `/expenses/*`
- Key files: `app/Http/Controllers/ExpenseController.php` (L1-L150), `routes/web.php` (L35-L40)

### Visits Management
- Features: Visit scheduling, tracking, location-based check-in/out
- Public API: Resource routes `/visits/*`, POST `/visits/checkin`, POST `/visits/checkout`
- Key files: `app/Http/Controllers/VisitController.php` (L1-L150), `routes/web.php` (L45-L50)

### Events Management
- Features: Event scheduling, attendee management, notifications
- Public API: Resource routes `/events/*`
- Key files: `app/Http/Controllers/EventController.php` (L1-L150), `routes/web.php` (L55-L60)

### Budgets Management
- Features: Budget planning, expense tracking, approval workflow
- Public API: Resource routes `/budgets/*`, POST `/budgets/{id}/complete`, POST `/budgets/{id}/submit-approval`
- Key files: `app/Http/Controllers/BudgetController.php` (L1-L200), `routes/web.php` (L65-L70)

### Assessments
- Features: Assessment creation, attempt tracking, result evaluation
- Public API: Resource routes `/assessments/*`
- Key files: `app/Http/Controllers/AssessmentController.php` (L1-L150), `routes/web.php` (L75-L80)

### Presentations
- Features: Presentation management, analytics, download tracking
- Public API: Resource routes `/presentations/*`, GET `/presentations/{id}/download`
- Key files: `app/Http/Controllers/PresentationController.php` (L1-L200), `app/Services/PresentationFileService.php` (L1-L100)

### Reports
- Features: Sales reports, expense reports, visit reports, custom reports, data export
- Public API: GET `/reports`, GET `/reports/sales`, GET `/reports/expenses`, POST `/reports/export`
- Key files: `app/Http/Controllers/ReportController.php` (L1-L200), `routes/web.php` (L140-L150)

### Notifications
- Features: Email notifications, queue management, daily digest, due email alerts
- Public API: Internal service APIs
- Key files: `app/Services/Mail/NotificationService.php` (L1-L100), `app/Services/Mail/PhpMailerService.php` (L1-L150)

## Core Functionalities & Flow

### Entry points:
- Web routes: `routes/web.php` (L1-L160) - Main application routes
- API routes: `routes/api.php` (L1-L98) - Location tracking and health check APIs
- Console commands: `routes/console.php` (L1-L20) - Scheduled tasks

### Services:
- NotificationService: Email notification management
- PhpMailerService: Email sending service
- BillFileService: Bill file upload handling
- PresentationFileService: Presentation file management
- PresentationTrackingService: Presentation analytics

### Data Flow Diagram:
```
[User] → [Auth Middleware] → [Controllers] → [Services] → [Models] → [Database]
                                     ↓
                              [Notification Queue] → [Email Service]
                                     ↓
                              [Scheduled Commands] → [Daily Digest]
```

## Dependencies/SDKs/Frameworks

### Backend Dependencies (composer.json L1-L77):
- **Laravel Framework**: ^11.31 - Core web framework
- **Laravel Sanctum**: ^4.0 - API authentication
- **Laravel Tinker**: ^2.10 - REPL for Laravel

### Frontend Dependencies (package.json L1-L21):
- **Alpine.js**: ^3.14.1 - Lightweight JavaScript framework
- **Axios**: ^1.7.4 - HTTP client
- **Tailwind CSS**: ^3.4.0 - Utility-first CSS framework
- **Vite**: ^5.0 - Build tool and dev server
- **Leaflet.js**: Used for location tracking maps (referenced in README-LOCATION-TRACKING.md)
- **OpenStreetMap**: Free map tiles for location tracking

### Development Dependencies:
- **PHPUnit**: ^11.4 - Testing framework
- **Laravel Pint**: ^1.18 - Code style fixer
- **Mockery**: ^1.6 - Mocking framework
- **Nunomaduro/Collision**: ^8.5 - Error reporting

## CLI Commands/Scripts, Environment Variables, and Configuration Files

### CLI Commands (routes/console.php L1-L20):
- `php artisan inspire` - Display inspiring quote
- `notifications:send-due` - Send due email notifications (runs every minute)
- `notifications:build-daily-digest` - Build daily digest emails (runs daily at 10:00 Asia/Dhaka)

### Custom Console Commands (app/Console/Commands/):
- `SendDueEmails.php` (L1-L48) - Handles due email notifications
- `BuildDailyDigest.php` - Builds daily digest emails

### Environment Variables (.env.example L1-L65):
- **Application**: APP_NAME, APP_ENV, APP_DEBUG, APP_URL, APP_LOCALE, APP_TIMEZONE
- **Database**: DB_CONNECTION (default: sqlite), DB_HOST, DB_PORT, DB_DATABASE
- **Mail**: MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_FROM_ADDRESS
- **AWS**: AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_DEFAULT_REGION, AWS_BUCKET
- **Session**: SESSION_DRIVER, SESSION_LIFETIME
- **Cache/Queue**: CACHE_STORE, QUEUE_CONNECTION
- **Logging**: LOG_CHANNEL, LOG_LEVEL

### Configuration Files:
- `config/app.php` - Application configuration
- `config/logging.php` - Logging configuration
- `composer.json` - PHP dependencies
- `package.json` - Node.js dependencies
- `vite.config.js` - Build tool configuration

## External Interfaces

### REST API Endpoints:

#### Authentication (routes/auth.php):
- POST `/login` - User authentication
- POST `/register` - User registration
- POST `/logout` - User logout
- POST `/forgot-password` - Password reset request
- POST `/reset-password` - Password reset

#### Location Tracking API (routes/api.php L1-L98):
- POST `/api/locations` - Store location data
- GET `/api/locations/latest` - Get latest locations
- GET `/api/locations/history` - Get location history

#### Health Check API:
- GET `/api/health` - API health check
- GET `/api/health/database` - Database health check

#### Web Routes (routes/web.php L1-L160):
- Resource routes for: orders, bills, expenses, visits, events, budgets, assessments, presentations
- Special endpoints: check-in/out, approvals, analytics, exports

### Implementation Details:
- **Authentication**: Laravel Sanctum token-based authentication
- **Rate Limiting**: Applied to location tracking API (minimum 10 seconds between updates)
- **Middleware**: Auth middleware applied to protected routes
- **CORS**: Configured for API endpoints

## Testing Overview

### Test Structure (tests/ directory):
- **Feature Tests** (`tests/Feature/`):
  - `Auth/` - Authentication flow tests (6 test files)
  - `ExampleTest.php` - Basic feature test example
  - `ProfileTest.php` - Profile management tests
- **Unit Tests** (`tests/Unit/`):
  - `ExampleTest.php` - Basic unit test example
- **Base Test Class**: `TestCase.php` - Custom test case base class

### Notable Test Cases:
- **Authentication Tests**: Registration, login, password reset, email verification
- **Profile Tests**: Profile update functionality
- **Example Tests**: Basic application functionality

### Testing Framework:
- **PHPUnit**: ^11.4 - Main testing framework
- **Mockery**: ^1.6 - Mocking library for unit tests
- **Laravel Testing**: Built-in Laravel testing utilities

## TODOs/Limitations/Tech Debt

### Current Status:
- **No TODO/FIXME comments found** in application code (searched in `app/` directory)
- **Debug mode enabled** in development environment (.env.example L4)
- **Logging level set to debug** in development (config/logging.php L64-L117)

### Potential Areas for Improvement:
- **Testing Coverage**: Limited test cases - only basic authentication and example tests
- **Documentation**: Standard Laravel README, could benefit from project-specific documentation
- **Error Handling**: Needs verification of comprehensive error handling across modules
- **Performance**: Database indexing and query optimization needs assessment
- **Security**: Security audit recommended for production deployment

### Technical Considerations:
- **Shared Hosting Compatibility**: Designed for Namecheap shared hosting
- **Free Technology Stack**: Uses only free/open-source technologies
- **PWA Support**: Location tracking module includes Progressive Web App features
- **Real-time Features**: Location tracking with automatic updates every 15-60 seconds

### Migration Notes:
- **Database Schema**: 35 migration files covering all modules
- **Location Tracking**: Dedicated migration for GPS tracking functionality
- **Email System**: Queue and template tables for notification system