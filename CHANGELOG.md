# Changelog

All notable changes to the `isend/laravel` package will be documented in this file.

## 1.0.2 - 2025-05-03

### Updated
- Updated PHP requirement to 8.2+ for better compatibility
- Updated PHPUnit to v11.5.3 to work with Orchestra Testbench v10.0

## 1.0.1 - 2025-05-03

### Updated
- Updated Orchestra Testbench to v10.0 for Laravel 12 compatibility

## 1.0.0 - 2025-05-03

### Added
- Initial release of the iSend SMS Laravel package
- Interactive CLI setup wizard via `php artisan isend:setup` command
- Support for sending SMS messages to single or multiple recipients
- Scheduled message delivery functionality
- Message status tracking and delivery reporting
- Campaign management features
- Account management (balance checking, profile info, sender IDs, transactions)
- Comprehensive exception handling with detailed error information
- Laravel Facade for easy integration
- Full support for Laravel 11.x and 12.x
- PHP 8.0+ compatibility
- Complete documentation with examples

### Fixed
- URL path handling to correctly separate base URL from API version path
- Proper normalization of API paths with and without leading slashes

### Security
- Secure API token management
- Request and response logging with sensitive data masking