
<p align="center">
    <img src="/art/nextsms-logo.png" width="300" title="NextSMS Logo" alt="NextSMS Logo">
</p>

# Laravel NextSMS

[![Latest Version on Packagist](https://img.shields.io/packagist/v/omakei/laravel-nextsms.svg?style=flat-square)](https://packagist.org/packages/omakei/laravel-nextsms)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/omakei/laravel-nextsms/run-tests?label=tests)](https://github.com/omakei/laravel-nextsms/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/omakei/laravel-nextsms/Check%20&%20fix%20styling?label=code%20style)](https://github.com/omakei/laravel-nextsms/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/omakei/laravel-nextsms.svg?style=flat-square)](https://packagist.org/packages/omakei/laravel-nextsms)

Laravel package to send SMS using NextSMS API.

## Installation

You can install the package via composer:

```bash
composer require omakei/laravel-nextsms
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="nextsms-config"
```

The following keys must be available in your `.env` file:

```bash
NEXTSMS_USERNAME=
NEXTSMS_PASSWORD=
NEXTSMS_SENDER_ID=
```

This is the contents of the published `config` file:

```php
return [
    'username' => env('NEXTSMS_USERNAME', 'NEXTSMS'),
    'password' => env('NEXTSMS_PASSWORD', 'NEXTSMS'),
    'api_key' => base64_encode(env('NEXTSMS_USERNAME', 'NEXTSMS').':'.env('NEXTSMS_PASSWORD', 'NEXTSMS')),
    'sender_id' => env('NEXTSMS_SENDER_ID', 'NEXTSMS'),
    'url' => [
        'sms' => [
            'single' => NextSMS::NEXTSMS_BASE_URL.'/api/sms/v1/text/single',
            'multiple' => NextSMS::NEXTSMS_BASE_URL.'/api/sms/v1/text/multi',
            'reports' => NextSMS::NEXTSMS_BASE_URL.'/api/sms/v1/reports',
            'logs' => NextSMS::NEXTSMS_BASE_URL.'/api/sms/v1/logs',
            'balance' => NextSMS::NEXTSMS_BASE_URL.'/api/sms/v1/balance',
        ],
        'sub_customer' => [
            'create' => NextSMS::NEXTSMS_BASE_URL.'/api/reseller/v1/sub_customer/create',
            'recharge' => NextSMS::NEXTSMS_BASE_URL.'/api/reseller/v1/sub_customer/recharge',
            'deduct' => NextSMS::NEXTSMS_BASE_URL.'/api/reseller/v1/sub_customer/deduct',
        ]
    ],
];
```

## Usage

### Send SMS

Sending single sms to single destination:

# Note: Make sure telephone number starts with country code. example: 255625933171


```php

use Omakei\NextSMS\NextSMS;

$response = NextSMS::sendSingleSMS(['to' => '255625933171', 'text' => 'Dj Omakei is texting.']);

```

Sending single sms to multiple destinations:

```php

use Omakei\NextSMS\NextSMS;

$response = NextSMS::sendSingleSMSToMultipleDestination([
            'to' => ['255625933171','255656699895'], 
            'text' => 'Dj Omakei is texting.']);

```

Sending multiple sms to multiple destinations (Example 1):

```php

use Omakei\NextSMS\NextSMS;

$response = NextSMS::sendMultipleSMSToMultipleDestinations(['messages' => [
                ['to' => '255625933171', 'text' => 'Dj Omakei is texting.'],
                ['to' => '255656699895', 'text' => 'Dj Omakei is texting.']
            ]]);

```

Sending multiple sms to multiple destinations (Example 2):

```php

use Omakei\NextSMS\NextSMS;

$response = NextSMS::sendMultipleSMSToMultipleDestinations(['messages' => [
                ['to' => ['255625933171','255656699895'], 'text' => 'Dj Omakei is texting.'],
                ['to' => '255625933171', 'text' => 'Dj Omakei is texting.']
            ]]);

```

Schedule sms:

```php

use Omakei\NextSMS\NextSMS;

$response = NextSMS::scheduleSMS([
            'to' => '255625933171', 
            'text' => 'Dj Omakei is texting.', 
            'date' => '2022-01-25' , 
            'time' => '12:00']);

```

### SMS Delivery Reports

Get all delivery reports:

```php

use Omakei\NextSMS\NextSMS;

$response = NextSMS::getAllDeliveryReports();

```

Get delivery reports with messageId:

```php

use Omakei\NextSMS\NextSMS;

$response = NextSMS::getDeliveryReportWithMessageId(243452542526627);

```

Get delivery reports with messageId:

```php

use Omakei\NextSMS\NextSMS;

$response = NextSMS::getDeliveryReportWithSpecificDateRange('2022-01-25', '2022-01-29');

```

### Sent Sms Logs

Get all sent SMS logs:

```php

use Omakei\NextSMS\NextSMS;

$response = NextSMS::getAllSentSMSLogs(10, 5);

```

Get all sent SMS logs with the optional parameter:

```php

use Omakei\NextSMS\NextSMS;

$response = NextSMS::getAllSentSMSLogsWithOptionalParameter('255625933171','2022-01-25', '2022-01-29',10, 5);

```

### Sub Customer

Register Sub Customer:

```php

use Omakei\NextSMS\NextSMS;

$response = NextSMS::subCustomerCreate(
            'Michael', 
            'Omakei',
            'omakei',
            'omakei96@gmail.com',
            '06259313171', 
            'Sub Customer (Reseller)', 
            100);

```

Recharge customer:

```php

use Omakei\NextSMS\NextSMS;

$response = NextSMS::subCustomerRecharge('omakei96@gmail.com', 100);

```

Deduct a customer:

```php

use Omakei\NextSMS\NextSMS;

$response = NextSMS::subCustomerDeduct('omakei96@gmail.com', 100);

```

Get sms balance:

```php

use Omakei\NextSMS\NextSMS;

$response = NextSMS::getSMSBalance();

```

## NextSMS API Documentation 

Please see [NextSMS Developer API](https://documenter.getpostman.com/view/4680389/SW7dX7JL#2936eed4-6027-45e7-92c9-fe1cd7df140b) for more details.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [omakei](https://github.com/omakei)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
