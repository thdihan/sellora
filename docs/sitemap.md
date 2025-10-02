# Sellora Navigation Sitemap

## Navigation Tree Structure

```
- Dashboard (/dashboard)
- Orders (/orders)
  - Create Order (/orders/create)
  - View Order (/orders/{id})
  - Edit Order (/orders/{id}/edit)
  - Approve Order (/orders/{id}/approve)
  - Download Attachment (/orders/{id}/attachments/{index})
- Bills (/bills)
  - Create Bill (/bills/create)
  - View Bill (/bills/{id})
  - Edit Bill (/bills/{id}/edit)
  - Approve Bill (/bills/{id}/approve)
  - Reject Bill (/bills/{id}/reject)
  - Mark Bill Paid (/bills/{id}/mark-paid)
  - Download Attachment (/bills/{id}/attachments/{index})
  - Remove Attachment (/bills/{id}/attachments/{index})
- Expenses (/expenses)
  - Create Expense (/expenses/create)
  - View Expense (/expenses/{id})
  - Edit Expense (/expenses/{id}/edit)
  - Approve Expense (/expenses/{id}/approve)
  - Reject Expense (/expenses/{id}/reject)
  - Mark Expense Paid (/expenses/{id}/mark-paid)
  - Download Attachment (/expenses/{id}/attachments/{index})
  - Remove Attachment (/expenses/{id}/attachments/{index})
- Reports (/reports) [Admin|Author|Manager|Chairman]
  - Sales Reports (/reports/sales)
  - Expenses Reports (/reports/expenses)
  - Visits Reports (/reports/visits)
  - Budgets Reports (/reports/budgets)
  - Custom Reports (/reports/custom)
  - Export Reports (/reports/export)
- Visits (/visits)
  - Create Visit (/visits/create)
  - View Visit (/visits/{id})
  - Edit Visit (/visits/{id}/edit)
  - Calendar View (/visits-calendar)
  - Calendar Events (/visits-calendar/events)
  - Start Visit (/visits/{id}/start)
  - Complete Visit (/visits/{id}/complete)
  - Reschedule Visit (/visits/{id}/reschedule)
  - Cancel Visit (/visits/{id}/cancel)
  - Download Attachment (/visits/{id}/attachments/{index})
  - Remove Attachment (/visits/{id}/attachments/{index})
- Budget (/budgets) [Admin|Manager|Chairman|Finance Manager]
  - Create Budget (/budgets/create)
  - View Budget (/budgets/{id})
  - Edit Budget (/budgets/{id}/edit)
  - Budget Analytics (/budgets/{id}/analytics)
  - Approve Budget (/budgets/{id}/approve)
  - Activate Budget (/budgets/{id}/activate)
  - Complete Budget (/budgets/{id}/complete)
  - Cancel Budget (/budgets/{id}/cancel)
  - Duplicate Budget (/budgets/{id}/duplicate)
  - Submit for Approval (/budgets/{id}/submit-approval)
  - Update Spending (/budgets/{id}/update-spending)
- Self Assessment (/self-assessments)
  - Create Self Assessment (/self-assessments/create)
  - View Self Assessment (/self-assessments/{id})
  - Edit Self Assessment (/self-assessments/{id}/edit)
  - Assessment History (/self-assessments-history)
  - Submit Assessment (/self-assessments/{id}/submit)
  - Revert to Draft (/self-assessments/{id}/revert-to-draft)
  - Mark as Reviewed (/self-assessments/{id}/mark-as-reviewed)
  - Export Assessment (/self-assessments/{id}/export)
- Events (/events)
  - Create Event (/events/create)
  - View Event (/events/{id})
  - Edit Event (/events/{id}/edit)
  - Calendar View (/events-calendar)
  - Calendar Events (/events-calendar/events)
  - Update Status (/events/{id}/status)
  - Duplicate Event (/events/{id}/duplicate)
  - Download Attachment (/events/{id}/attachments/{index})
  - Remove Attachment (/events/{id}/attachments/{index})
  - Upcoming Events (/upcoming-events)
- Location Tracker (/locations) [Admin|Manager|Chairman]
  - Create Location (/locations/create)
  - View Location (/locations/{id})
  - Edit Location (/locations/{id}/edit)
  - Check In (/locations/{id}/check-in)
  - Check Out (/locations/{id}/check-out)
  - Nearby Locations (/locations/nearby)
  - Toggle Favorite (/locations/{id}/toggle-favorite)
  - Location Analytics (/locations/{id}/analytics)
  - Export Location (/locations/{id}/export)
  - Location Settings (/locations/settings)
  - Location Dashboard (/locations/dashboard)
  - Track (/track)
  - Team Map (/team-map)
- Products (/products)
  - Manual Entry
    - Create Product (/products/create)
    - View Product (/products/{id})
    - Edit Product (/products/{id}/edit)
  - API Sync (/products/sync)
  - Import
    - Import Index (/products/import)
    - SQL Import (/products/import/sql)
    - CSV Import (/products/import/csv)
    - Excel Import (/products/import/excel)
    - Full DB Import (/products/import/full-db)
- Inventory (/inventory)
  - Inventory Overview (/inventory)
  - Batches (/inventory/batches)
  - Adjustments (/inventory/adjustments)
  - Transfers (/inventory/transfers)
  - Transactions (/inventory/transactions)

- Assessments (/assessments)
  - Create Assessment (/assessments/create)
  - View Assessment (/assessments/{id})
  - Edit Assessment (/assessments/{id}/edit)
  - Take Assessment (/assessments/{id}/take)
  - Assessment Attempt (/assessments/{id}/attempt/{attempt})
  - Submit Assessment (/assessments/{id}/attempt/{attempt}/submit)
  - Assessment Results (/assessments/{id}/attempt/{attempt}/results)
  - Assessment Analytics (/assessments/{id}/analytics)
  - Duplicate Assessment (/assessments/{id}/duplicate)
  - Toggle Status (/assessments/{id}/toggle-status)
  - Export Assessment (/assessments/{id}/export)
- Presentations (/presentations)
  - Create Presentation (/presentations/create)
  - View Presentation (/presentations/{id})
  - Edit Presentation (/presentations/{id}/edit)
  - Download Presentation (/presentations/{id}/download)
  - Duplicate Presentation (/presentations/{id}/duplicate)
  - Export Presentations (/presentations/export)
  - Auto Reports (/presentations/auto-reports)
  - Generate From Report (/presentations/generate-from-report/{report})
  - Presentation Analytics (/presentations/{id}/analytics)
- Administration [Admin/Author]
  - User Management (/users) [Author]
    - Create User (/users/create)
    - View User (/users/{id})
    - Edit User (/users/{id}/edit)
    - Verify Email (/users/{id}/verify-email)
    - Activate User (/users/{id}/activate)
    - Deactivate User (/users/{id}/deactivate)
    - Bulk Update (/users/bulk-update)
  - Role Management (/roles) [Author]
    - Create Role (/roles/create)
    - View Role (/roles/{id})
    - Edit Role (/roles/{id}/edit)
    - Update Permissions (/roles/{id}/permissions)
  - Tax Management (/tax) [Author]
    - VAT/TAX Settings (/tax)
    - Create Tax (/tax/create)
    - View Tax (/tax/{id})
    - Edit Tax (/tax/{id}/edit)
    - Tax Heads (/admin/tax-heads)
  - API Connector (/api-connector) [Author]
    - API Dashboard (/api-connector/dashboard)
    - Get Config (/api-connector/config/{system})
    - Update Config (/api-connector/config/{system})
  - System Settings (/settings)
    - Profile Settings (/settings/profile)
    - Company Settings (/settings/company)
    - Application Settings (/settings/app)
    - Email/SMTP Settings (/settings/email)
      - Email Test (/settings/email/test)
    - Theme/Branding (/settings/theme)
    - Security Settings (/settings/security)
    - Backup Settings (/settings/backup)
    - Update Settings (/settings/updates)
      - Upload Update (/settings/updates/upload)
      - Update Settings (/settings/updates/settings)
      - Rollback Version (/settings/updates/rollback/{version})
      - Clear Cache (/settings/updates/clear-cache)
      - View Logs (/settings/updates/logs)
    - Footer Brand (/settings/footer-brand)
  - Data Import (/settings/import) [Author]
  - Data Export (/settings/export) [Author]
- Settings (/settings)
  - Profile (/settings/profile)
  - Theme (/settings/theme)
```

## Breadcrumbs for Key Pages

### Core Features
- **Dashboard**: Dashboard
- **Orders**: Dashboard > Orders
- **Create Order**: Dashboard > Orders > Create Order
- **Bills**: Dashboard > Bills
- **Create Bill**: Dashboard > Bills > Create Bill
- **Expenses**: Dashboard > Expenses
- **Create Expense**: Dashboard > Expenses > Create Expense

### Product Management
- **Products**: Dashboard > Products
- **Manual Entry**: Dashboard > Products > Manual Entry
- **API Sync**: Dashboard > Products > API Sync
- **SQL Import**: Dashboard > Products > Import > SQL Import
- **CSV Import**: Dashboard > Products > Import > CSV Import
- **Excel Import**: Dashboard > Products > Import > Excel Import
- **Full DB Import**: Dashboard > Products > Import > Full DB Import

### Inventory Management
- **Inventory**: Dashboard > Inventory
- **Inventory Batches**: Dashboard > Inventory > Batches
- **Inventory Adjustments**: Dashboard > Inventory > Adjustments
- **Inventory Transfers**: Dashboard > Inventory > Transfers


### Reports & Analytics
- **Reports**: Dashboard > Reports
- **Sales Reports**: Dashboard > Reports > Sales Reports
- **Custom Reports**: Dashboard > Reports > Custom Reports
- **Presentations**: Dashboard > Presentations
- **Auto Reports**: Dashboard > Presentations > Auto Reports

### Administration
- **User Management**: Dashboard > Administration > User Management
- **Role Management**: Dashboard > Administration > Role Management
- **Tax Management**: Dashboard > Administration > Tax Management
- **VAT/TAX Settings**: Dashboard > Administration > Tax Management > VAT/TAX Settings
- **API Connector**: Dashboard > Administration > API Connector
- **System Settings**: Dashboard > Administration > System Settings
- **Email Settings**: Dashboard > Administration > System Settings > Email/SMTP Settings
- **Theme Settings**: Dashboard > Administration > System Settings > Theme/Branding
- **Data Import**: Dashboard > Administration > Data Import
- **Data Export**: Dashboard > Administration > Data Export

### Settings
- **Profile Settings**: Dashboard > Settings > Profile
- **Theme Settings**: Dashboard > Settings > Theme
- **Email Test**: Dashboard > Settings > Email > Test Email

### CRM & Tracking
- **Visits**: Dashboard > Visits
- **Events**: Dashboard > Events
- **Location Tracker**: Dashboard > Location Tracker
- **Team Map**: Dashboard > Location Tracker > Team Map

### HR & Assessment
- **Self Assessment**: Dashboard > Self Assessment
- **Assessments**: Dashboard > Assessments
- **Budget**: Dashboard > Budget

## Role-Based Access Summary

- **Guest**: Login, Register, Forgot Password, Reset Password
- **Authenticated Users**: Dashboard, Orders, Bills, Expenses, Visits, Events, Products, Inventory, Assessments, Presentations, Self Assessment, Profile Settings, Theme Settings
- **Admin|Author|Manager|Chairman**: Reports (all types)
- **Admin|Manager|Chairman|Finance Manager**: Budget Management
- **Admin|Manager|Chairman**: Location Tracker
- **Author Only**: User Management, Role Management, Tax Management, API Connector, Data Import/Export
- **Admin**: System Settings (Company, Application, Email, Security, Backup, Updates)