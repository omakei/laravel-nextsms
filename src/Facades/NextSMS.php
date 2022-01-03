<?php

namespace Omakei\NextSMS\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Omakei\NextSMS\NextSMS
 */
class NextSMS extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-nextsms';
    }
}
