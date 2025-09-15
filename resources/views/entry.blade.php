<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" sizes="32x32" href="/ihand-512.png" />
        <link rel="apple-touch-icon"  href="/ihand-512.png" />
        <meta name="description" content="POS Single Entry Multiplatform App. Tersedia versi build Windows, MacOS dan Linux" />
        <link rel="canonical" href="https://ihandcashier.basapadi.com" />
        <title>Ihand Cashier</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Rubik&display=swap" rel="stylesheet">
        @vite('resources/js/app.js')
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
