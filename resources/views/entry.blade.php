<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="/favicon.ico" />
        <link rel="icon" type="image/png" sizes="32x32" href="/ihand-512.png" />
        <link rel="apple-touch-icon"  href="/ihand-512.png" />
        <meta name="description" content="Simple POS Ihand Cashier Mobile and Desktop for your bussiness" />
        <link rel="canonical" href="https://example.com/halaman" />
        <title>IhandCashier</title>
        @vite('resources/js/app.js')
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
