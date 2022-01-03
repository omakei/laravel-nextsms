<?php

namespace Omakei\NextSMS\Commands;

use Illuminate\Console\Command;

class NextSMSCommand extends Command
{
    public $signature = 'laravel-nextsms';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
