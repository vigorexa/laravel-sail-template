<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    $readmePath = base_path('readme.md');

    abort_unless(is_readable($readmePath), 404);

    return view('welcome', [
        'content' => Str::markdown(file_get_contents($readmePath)),
    ]);
});
