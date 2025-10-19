<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" sizes="32x32" href="/icon.png" />
        <link rel="apple-touch-icon"  href="/icon.png" />
        <meta name="description" content="Simple POS Multiplatform App. Tersedia versi build Windows, MacOS dan Linux" />
        <link rel="canonical" href="https://ihandcashier.basapadi.com" />
        <title>Ihand Cashier</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <!-- <link href="https://fonts.googleapis.com/css2?family=Rubik&display=swap" rel="stylesheet"> -->
        <!-- <link href="ia-writer-quattro-latin-400-normal.ttf" rel="stylesheet"> -->

        <meta property="og:title" content="Simple POS Multiplatform App. Tersedia versi build Windows, MacOS dan Linux - Ihand Cashier">
        <meta property="og:description" content="Aplikasi yang dapat membantu mengelola bisnis anda">
        <meta property="og:type" content="website"> <!-- article jika konten artikel -->
        <meta property="og:url" content="https://ihandcashier.basapadi.com">
        <meta property="og:site_name" content="Ihand Cashier">
        <meta property="og:image" content="/splash.png">
        <meta property="og:image:alt" content="Logo Ihand Cashier">
        <meta property="og:image:type" content="image/png">
        <meta property="og:locale" content="id_ID">
        @vite('resources/js/app.js')
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
