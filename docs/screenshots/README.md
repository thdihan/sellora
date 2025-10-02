# Screenshots Directory

This directory contains failure screenshots captured during QA testing.

## Current Screenshots

- No failure screenshots captured (all critical tests passed)
- Screenshots would be automatically generated for:
  - Button click failures
  - Form validation errors
  - Network timeout issues
  - UI rendering problems

## Screenshot Naming Convention

- `{module}_{action}_{timestamp}.png`
- Example: `bills_create_without_attachment_20250115_103025.png`

## Notes

- Screenshots are captured automatically during test failures
- All current issues are functional (not visual) so no screenshots needed
- Future test runs will populate this directory with actual failure screenshots