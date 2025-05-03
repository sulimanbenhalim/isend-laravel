# iSend SMS Laravel Package

[![GitHub License](https://img.shields.io/github/license/sulimanbenhalim/isend-laravel)](https://github.com/sulimanbenhalim/isend-laravel/blob/main/LICENSE.md)
[![PHP Version Support](https://img.shields.io/badge/php-%3E%3D%208.2-blue)](https://www.php.net/supported-versions.php)
[![Laravel Version Support](https://img.shields.io/badge/laravel-%3E%3D%2011.0-red)](https://laravel.com/docs/11.x/releases)

A Laravel package for sending SMS messages using the iSend SMS API v3. Simple to use and easy to set up.

## Key Features

- SMS messaging with single/multiple recipients
- Message scheduling and delivery tracking
- Campaign management
- Error handling with detailed diagnostics
- Interactive CLI setup wizard

## Requirements

- PHP 8.2 or higher
- Laravel 11.x or 12.x
- Guzzle HTTP client
- Valid iSend SMS API credentials

## Installation

You can install the package via Composer:

```bash
composer require isend/laravel
```

The package will automatically register itself using Laravel's package discovery.

## Configuration

### Interactive Setup (Recommended)

The easiest way to set up the package is by using the interactive setup command:

```bash
php artisan isend:setup
```

This command will set up your API credentials and publish the config file in one step.

### Or, Manual Configuration

Alternatively, you can manually publish the configuration file:

```bash
php artisan vendor:publish --tag="isend-config"
```

This will create a `config/isend.php` file with comprehensive configuration options.

Then, add these environment variables to your `.env` file:

```
ISEND_API_TOKEN=your-api-token
ISEND_DEFAULT_SENDER_ID=your-sender-id
```

### Optional Environment Variables

You can customize the service with these additional variables:

```
ISEND_BASE_URL=https://isend.com.ly
ISEND_API_VERSION_PATH=/api/v3
```

## Basic Usage

### Sending a Simple SMS

```php
use ISend\SMS\Facades\ISend;

// Fluent interface
ISend::to('218929000834')
    ->message('Your verification code is 1234')
    ->send();

// Get the SMS ID for tracking
$smsId = ISend::to('218929000834')
    ->message('Your verification code is 1234')
    ->send()
    ->getId();

// Static helper method
ISend::sendSms('218929000834', 'Your verification code is 1234');
```

### Multiple Recipients

```php
// Send the same message to multiple recipients
ISend::to(['218929000834', '218929000836', '218929000837'])
    ->message('Important announcement for all users')
    ->send();

// Or as a comma-separated string
ISend::to('218929000834,218929000836,218929000837')
    ->message('Important announcement for all users')
    ->send();
```

### Custom Sender ID

```php
ISend::to('218929000834')
    ->from('MyApp')  // Override the default sender ID
    ->message('Your verification code is 1234')
    ->send();
```

### Scheduled Messages

```php
// Schedule a message for future delivery
ISend::to('218929000834')
    ->message('Reminder: Your appointment is tomorrow')
    ->scheduleAt('2025-12-31 09:00:00')  // Format: Y-m-d H:i
    ->send();
```

### DLT Template ID (for regulatory compliance)

```php
// For regions requiring DLT template registration
ISend::to('218929000834')
    ->message('Your OTP is 1234')
    ->dltTemplateId('template-123')
    ->send();
```

## Advanced Features

### Checking Message Status

```php
// Get the status of a sent message using its ID
$status = ISend::getStatus('sms-uid-123456');

// Example response structure
[
    'status' => 'success',
    'data' => [
        'uid' => 'sms-uid-123456',
        'status' => 'delivered',
        'sent_at' => '2025-01-01 12:00:00',
        'delivered_at' => '2025-01-01 12:00:05',
        // Additional status details...
    ]
]
```

### Listing Messages

```php
// Get a list of all sent messages
$messages = ISend::listMessages();
```

### Campaign Management

```php
// Send a campaign to a contact list
$campaign = ISend::sendCampaign(
    'contact-list-123',
    'Special offer for our valued customers!',
    'Marketing',  // Optional custom sender ID
    '2025-02-15 08:00:00'  // Optional schedule time
);

// Send to multiple contact lists
$campaign = ISend::sendCampaign(
    ['list-123', 'list-456'],
    'Special offer for all our customers!'
);
```

### Account Management

```php
// Get profile information
$profile = ISend::getProfile();

// Check account balance
$balance = ISend::checkBalance();

// Get registered sender IDs
$senderIds = ISend::getSenderIds();

// Get transaction history
$transactions = ISend::getTransactions();
```

## Exception Handling

The package throws `ISendException` with detailed error information:

```php
use ISend\SMS\Exceptions\ISendException;

try {
    ISend::to('invalid-number')
        ->message('Test message')
        ->send();
} catch (ISendException $e) {
    // Basic error information
    $message = $e->getMessage();
    $statusCode = $e->getStatusCode();
    
    // Detailed error data
    $responseData = $e->getResponseData();  // Full API response
    $requestData = $e->getRequestData();    // The request that caused the error
    
    // Error type helpers
    if ($e->isAuthenticationError()) {
        // Handle 401 errors (invalid API token)
    }
    
    if ($e->isValidationError()) {
        // Handle 422 errors (invalid parameters)
    }
    
    if ($e->isServerError()) {
        // Handle 5xx errors (server issues)
    }
    
    // Get complete error details as array
    $errorDetails = $e->toArray();
    
    // Log the error or handle as needed...
}
```

## Debugging

For debugging purposes, you can access the last API response:

```php
$response = ISend::to('218929000834')
    ->message('Test message')
    ->send()
    ->getLastResponse();
```

## Testing

The package includes a comprehensive test suite:

```bash
composer test
```

## Laravel Version Compatibility

| Laravel Version | Package Version |
|-----------------|-----------------|
| 11.x            | 1.x             |
| 12.x            | 1.x             |

## Security

If you discover any security issues, please email soliman.benhalim@gmail.com instead of using the issue tracker.

## Credits

- [iSend SMS Service](https://isend.com.ly)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.