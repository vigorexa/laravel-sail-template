<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
    </head>
    <body>
        <h1>{{ config('app.name') }}</h1>
        <p>Laravel Sail template</p>
        <ul>
            <li><a href="/panel">Admin panel</a></li>
            <li><a href="/up">Health check</a></li>
        </ul>
    </body>
</html>
