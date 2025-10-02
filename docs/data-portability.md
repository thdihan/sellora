# Data Portability System

This document describes the comprehensive data import/export system implemented in Sellora.

## Overview

The data portability system allows authorized users to import and export data in multiple formats including CSV, Excel, SQL dumps, and JSON. The system includes robust validation, progress tracking, and security controls.

## Features

### Import Capabilities
- **File Formats**: CSV, Excel (.xlsx, .xls), SQL dumps
- **Field Mapping**: Interactive mapping between file columns and database fields
- **Data Validation**: Real-time validation with error reporting
- **Progress Tracking**: Live progress updates during import
- **Background Processing**: Large imports handled via queue jobs
- **Error Handling**: Detailed error logs and recovery options

### Export Capabilities
- **File Formats**: CSV, Excel, SQL, JSON
- **Data Selection**: Choose specific tables and date ranges
- **Scheduling**: Schedule exports for later execution
- **Download Management**: Secure download links with expiration
- **Compression**: Automatic compression for large exports

## Security & Access Control

### Role-Based Access Control (RBAC)
Access to import/export functionality is restricted to:
- **Admin**: Full access to all import/export features
- **Author**: Full access to all import/export features
- **Manager**: Full access to all import/export features
- **Other roles**: No access (403 Forbidden)

### SQL Security
- Only safe SQL statements allowed (INSERT, UPDATE, SELECT)
- Dangerous operations blocked (DROP, DELETE, TRUNCATE, ALTER)
- SQL injection protection via parameterized queries
- Statement validation before execution

### File Security
- File type validation and MIME type checking
- Size limits enforced
- Secure file storage with unique naming
- Automatic cleanup of temporary files

## API Endpoints

### Import Endpoints
```
GET    /api/imports           - List import jobs
POST   /api/imports           - Create new import job
GET    /api/imports/{id}      - Get import job details
DELETE /api/imports/{id}      - Delete import job
GET    /api/imports/presets   - Get import presets
POST   /api/imports/presets   - Create import preset
```

### Export Endpoints
```
GET    /api/exports           - List export jobs
POST   /api/exports           - Create new export job
GET    /api/exports/{id}      - Get export job details
GET    /api/exports/{id}/download - Download export file
DELETE /api/exports/{id}      - Delete export job
```

## Database Models

### ImportJob
Tracks import operations with fields for progress, mapping, and error handling.

### ExportJob
Manages export operations including file generation and download tracking.

### ImportItem
Stores individual import records with validation status and error details.

### AuditLog
Logs all import/export activities for compliance and debugging.

## Background Jobs

### ProcessImportJob
Handles large import operations in the background:
- Parses uploaded files
- Validates data according to mapping rules
- Processes records in batches
- Updates progress in real-time
- Handles errors gracefully

### ProcessExportJob
Manages export generation:
- Queries data based on selection criteria
- Generates files in requested format
- Compresses large exports
- Sends completion notifications

## Service Classes

### SqlParserService
Provides safe SQL parsing and validation:
- Validates SQL statements for security
- Blocks dangerous operations
- Parses multi-statement SQL files
- Generates security warnings

### CsvProcessorService
Handles CSV file operations:
- Auto-detects delimiters and encoding
- Validates CSV structure
- Processes large files efficiently
- Generates sample CSV templates

### ExcelProcessorService
Manages Excel file processing:
- Supports multiple Excel formats
- Handles multiple worksheets
- Validates data types and formats
- Exports to Excel with formatting

## User Interface

### Import Wizard
1. **File Upload**: Drag-and-drop or browse file selection
2. **Format Detection**: Automatic file type and structure detection
3. **Field Mapping**: Interactive mapping interface
4. **Validation**: Real-time data validation preview
5. **Progress**: Live progress tracking during import

### Export Interface
1. **Data Selection**: Choose tables and date ranges
2. **Format Options**: Select output format and options
3. **Scheduling**: Optional scheduling for later execution
4. **Download**: Secure download management

## Configuration

### File Limits
- Maximum file size: 50MB
- Supported formats: CSV, Excel, SQL, JSON
- Batch processing: 1000 records per batch
- Queue timeout: 30 minutes

### Storage
- Import files: `storage/app/imports/`
- Export files: `storage/app/exports/`
- Temporary files: `storage/app/temp/`
- Auto-cleanup: 7 days retention

## Error Handling

### Import Errors
- File format validation errors
- Data type conversion errors
- Constraint violation errors
- Mapping configuration errors

### Export Errors
- Data access permission errors
- File generation errors
- Storage space errors
- Format conversion errors

## Testing

Comprehensive test suite includes:
- Unit tests for all models and services
- Feature tests for API endpoints
- Integration tests for file processing
- Security tests for RBAC and SQL validation

## Usage Examples

### Basic CSV Import
```javascript
// Upload CSV file with automatic mapping
const formData = new FormData();
formData.append('file', csvFile);
formData.append('file_type', 'csv');
formData.append('options', JSON.stringify({has_header: true}));

fetch('/api/imports', {
    method: 'POST',
    body: formData
});
```

### Scheduled Export
```javascript
// Schedule export for later execution
fetch('/api/exports', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        tables: ['users', 'orders'],
        format: 'excel',
        schedule_at: '2024-01-15 02:00:00'
    })
});
```

## Troubleshooting

### Common Issues
1. **File Upload Fails**: Check file size and format
2. **Import Stalls**: Verify queue workers are running
3. **Permission Denied**: Confirm user role permissions
4. **SQL Errors**: Review SQL statement validation

### Logs
- Application logs: `storage/logs/laravel.log`
- Queue logs: `storage/logs/queue.log`
- Import/Export logs: Audit log table

## Maintenance

### Regular Tasks
- Clean up old import/export files
- Monitor queue job performance
- Review audit logs for security
- Update file size limits as needed

### Performance Optimization
- Use database indexing for large datasets
- Configure queue workers for peak usage
- Monitor storage space usage
- Optimize batch sizes for imports

## Security Considerations

### Best Practices
- Regular security audits of uploaded files
- Monitor for suspicious SQL patterns
- Implement rate limiting for API endpoints
- Regular backup of audit logs
- Keep file retention policies updated

### Compliance
- GDPR compliance for data exports
- Audit trail for all operations
- Secure file handling and cleanup
- Role-based access controls

For technical support or feature requests, please contact the development team.