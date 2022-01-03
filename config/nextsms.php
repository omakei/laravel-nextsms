<?php


use Omakei\NextSMS\NextSMS;

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
