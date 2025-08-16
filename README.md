## About IhandCashier Project
#### Requirement
- PHP 8.4
- NodeJS 22+
- [Laravel 12](https://laravel.com/docs/12.x/)
- [Native PHP v1+](https://nativephp.com/docs/desktop/1/getting-started/introduction)

#### Initial Instalation
- Clone repository
- Jalankan `composer install` dan `npm install` pada root project
- Copy `.env.example` dan ubah namanya menjadi `.env` dan sesuaikan value pada configurasi .env
- Jalankan `php artisan key:generate` untuk generate key
- Pastikan database menggunakan sqlite
- Jalankan `php artisan migrate` dan `php artisan db:seed`
- Untuk versi desktop jalankan `php artisan native:migrate` dan `php artisam native:seed`

#### Development
- Kamu bisa menjalan laravel dan nodejs secara terpisah dengan cara menjalankan command `php artisan native:serve` kemudian jalankan perintah di terminal terpisah `npm run dev` untuk versi websitenya. Info lengkapnya bisa dilihat di web berikut (https://nativephp.com/docs/desktop/1/getting-started/development)
- atau kamu bisa menjalan keduanya dalam satu perintah untuk versi desktop dan webnya dengan command `composer native:dev`
- untuk build aplikasi desktopnya mengunakan command `php artisan native:build` dan pilih OS yang sesuai untuk anda, contoh `php artisan native:build win` untuk OS windows. Info lebih lengkapnya bisa lihat di artikel berikut (https://nativephp.com/docs/desktop/1/publishing/building)
