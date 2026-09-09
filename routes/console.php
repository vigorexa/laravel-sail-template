<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;

Artisan::command('inspire', function (): void {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
