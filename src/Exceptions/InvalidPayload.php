<?php


namespace Omakei\NextSMS\Exceptions;


use Exception;

final class InvalidPayload extends Exception
{
    public static function invalidSingleSMSPayload(): InvalidPayload
    {
        return new InvalidPayload('The payload provided must contain "to" and "text" keys.');
    }

    public static function invalidMultipleSMSPayload(): InvalidPayload
    {
        return new InvalidPayload('The payload provided must contain "to" key of type array.');
    }

    public static function invalidPayloadArrayKeys(string $keys): InvalidPayload
    {
        return new InvalidPayload("The payload provided must contain {$keys} key(s).");
    }

    public static function invalidPayloadArrayKeyType(string $key_type): InvalidPayload
    {
        return new InvalidPayload("The payload provided must contain {$key_type}.");
    }

    public static function invalidAccountType(): InvalidPayload
    {
        return new InvalidPayload('The account type must be Sub Customer or Sub Customer (Reseller).');
    }

}
