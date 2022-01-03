<?php

namespace Omakei\NextSMS;

use Omakei\NextSMS\Commands\NextSMSCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NextSMSServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-nextsms')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-nextsms_table')
            ->hasCommand(NextSMSCommand::class);
    }
}
