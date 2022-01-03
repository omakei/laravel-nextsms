<?php

namespace Omakei\NextSMS;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Omakei\NextSMS\Exceptions\InvalidPayload;

class NextSMS
{
    public const NEXTSMS_BASE_URL = 'https://messaging-service.co.tz';

    public static function sendSingleSMS(array $payload): Response
    {
        return self::makePostRequest(
            config('nextsms.url.sms.single'),
            self::getSingleSMSPayload($payload)
        );
    }

    public static function sendSingleSMSToMultipleDestination(array $payload): Response
    {
        return self::makePostRequest(
            config('nextsms.url.sms.single'),
            self::getSingleSMSToMultipleDestinationPayload($payload)
        );
    }

    public static function sendMultipleSMSToMultipleDestinations(array $payload): Response
    {
        return self::makePostRequest(
            config('nextsms.url.sms.multiple'),
            self::getMultipleSMSToMultipleDestinationPayload($payload)
        );
    }

    public static function scheduleSMS(array $payload): Response
    {
        return self::makePostRequest(
            config('nextsms.url.sms.single'),
            self::getScheduleSMSPayload($payload)
        );
    }

    public static function getAllDeliveryReports(): Response
    {
        return self::makeGetRequest(config('nextsms.url.sms.reports'), []);
    }

    public static function getDeliveryReportWithMessageId(int $messageId): Response
    {
        return self::makeGetRequest(config('nextsms.url.sms.reports'), ['messageId' => $messageId]);
    }

    public static function getDeliveryReportWithSpecificDateRange(string $sentSince, string $sentUntil): Response
    {
        throw_if(count(explode('-', $sentSince)) != 3 || count(explode('-', $sentUntil)) != 3, InvalidPayload::invalidPayloadArrayKeyType('"date" key of format "YYYY-MM-DD"'));

        $dateSince = explode('-', $sentSince);

        $dateUnit = explode('-', $sentUntil);

        throw_if(checkdate($dateSince[1], $dateSince[2], $dateSince[0]) ||
            checkdate($dateUnit[1], $dateUnit[2], $dateUnit[0]), InvalidPayload::invalidPayloadArrayKeyType('"date" key of format "YYYY-MM-DD"'));

        return self::makeGetRequest(
            config('nextsms.url.sms.reports'),
            ['sentSince' => $sentSince, 'sentUntil' => $sentUntil]
        );
    }

    public static function getAllSentSMSLogs(int $limit, int $offset): Response
    {
        return self::makeGetRequest(config('nextsms.url.sms.reports'), [
            'from' => config('nextsms.sender_id'),
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    public static function getAllSentSMSLogsWithOptionalParameter(
        string $to,
        string $sentSince,
        string $sentUntil,
        int $limit,
        int $offset
    ): Response {
        throw_if(count(explode('-', $sentSince)) != 3 || count(explode('-', $sentUntil)) != 3, InvalidPayload::invalidPayloadArrayKeyType('"date" key of format "YYYY-MM-DD"'));

        $dateSince = explode('-', $sentSince);

        $dateUnit = explode('-', $sentUntil);

        throw_if(checkdate($dateSince[1], $dateSince[2], $dateSince[0]) ||
            checkdate($dateUnit[1], $dateUnit[2], $dateUnit[0]), InvalidPayload::invalidSingleSMSPayload());


        return self::makeGetRequest(config('nextsms.url.sms.reports'), [
            'from' => config('nextsms.sender_id'),
            'to' => $to,
            'sentSince' => $sentSince,
            'sentUntil' => $sentUntil,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    public static function getSMSBalance(): Response
    {
        return self::makeGetRequest(config('nextsms.url.sms.balance'), []);
    }

    public static function subCustomerCreate(
        string $first_name,
        string $last_name,
        string $username,
        string $email,
        string $phone_number,
        string $account_type,
        int $sms_price
    ): Response {
        throw_if($account_type != 'Sub Customer' ||
            $account_type != 'Sub Customer (Reseller)', InvalidPayload::invalidAccountType());

        return self::makePostRequest(config('nextsms.url.sub_customer.create'), [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'username' => $username,
            'email' => $email,
            'phone_number' => $phone_number,
            'account_type' => $account_type,
            'sms_price' => $sms_price,
        ]);
    }

    public static function subCustomerRecharge(string $email, int $smscount): Response
    {
        return self::makePostRequest(config('nextsms.url.sub_customer.recharge'), [
            'email' => $email,
            'smscount' => $smscount,
        ]);
    }

    public static function subCustomerDeduct(string $email, int $smscount): Response
    {
        return self::makePostRequest(config('nextsms.url.sub_customer.deduct'), [
            'email' => $email,
            'smscount' => $smscount,
        ]);
    }

    protected static function makePostRequest(string $url, mixed $payload): Response
    {
        return Http::withHeaders(self::getHeaders())->post($url, $payload);
    }

    protected static function makeGetRequest(string $url, mixed $payload): Response
    {
        return Http::withHeaders(self::getHeaders())->get($url, $payload);
    }

    protected static function getHeaders(): array
    {
        return  [
            'Authorization' => 'Basic ' . config('nextsms.api_key'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    protected static function getSingleSMSPayload(array $payload): array
    {
        throw_if(
            count(array_diff(['to','text'], array_keys($payload))) != 0,
            InvalidPayload::invalidSingleSMSPayload()
        );

        return array_merge(['from' => config('textsms.sender_id'), $payload]);
    }

    protected static function getSingleSMSToMultipleDestinationPayload(array $payload): array
    {
        throw_if(
            count(array_diff(['to','text'], array_keys($payload))) != 0,
            InvalidPayload::invalidSingleSMSPayload()
        );

        throw_if(! is_array($payload['to']), InvalidPayload::invalidMultipleSMSPayload());

        return array_merge(['from' => config('textsms.sender_id'), $payload]);
    }

    protected static function getMultipleSMSToMultipleDestinationPayload(array $payload): array
    {
        throw_if(
            count(array_diff(['messages'], array_keys($payload))) != 0,
            InvalidPayload::invalidPayloadArrayKeys('"messages"')
        );

        throw_if(
            ! is_array($payload['messages']),
            InvalidPayload::invalidPayloadArrayKeyType('"messages" key of type array')
        );


        $payloads = [];
        $index = 0;

        foreach ($payload['messages'] as $object) {
            throw_if(
                count(array_diff(['to','text'], array_keys($object))) != 0,
                InvalidPayload::invalidSingleSMSPayload()
            );

            throw_if(
                ! is_array(array_column($object, 'to')) ||
                ! is_string(array_column($object, 'to')),
                InvalidPayload::invalidPayloadArrayKeyType('"to" key of type array or "to" key of type string')
            );


            $payloads['messages'][$index] = array_merge(['from' => config('textsms.sender_id'), $object]);

            $index++;
        }

        return $payloads;
    }

    protected static function getScheduleSMSPayload(array $payload): array
    {
        throw_if(
            count(array_diff(['to','text', 'date', 'time'], array_keys($payload))) != 0,
            InvalidPayload::invalidPayloadArrayKeys('"to", "text", "date" and "time"')
        );

        throw_if(
            count(explode('-', $payload['date'])) != 3,
            InvalidPayload::invalidPayloadArrayKeyType('"date" key of format "YYYY-MM-DD"')
        );

        throw_if(
            count(explode(':', $payload['time'])) != 2,
            InvalidPayload::invalidPayloadArrayKeyType('"time" key of format "HH:MM"')
        );

        $date = explode('-', $payload['date']);

        throw_if(
            ! checkdate($date[1], $date[2], $date[0]),
            InvalidPayload::invalidPayloadArrayKeyType('"date" key of format "YYYY-MM-DD"')
        );

        throw_if(
            ! preg_match("/(2[0-3]|[0][0-9]|1[0-9]):([0-5][0-9])/", $payload['time']),
            InvalidPayload::invalidPayloadArrayKeyType('"time" key of format "HH:MM"')
        );

        return array_merge(['from' => config('textsms.sender_id'), $payload]);
    }
}
