<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

## ULE-shop on Laravel by Sadkou

[Laravel](https://laravel.com/docs/routing) is a web application framework with expressive, elegant syntax.


There is [DEMO](http://uls.northeurope.cloudapp.azure.com/) ULE-shop.

<p><img src="https://photos.app.goo.gl/bgDVWGUz8q7ajC182"></p>

1. Setup (install/create) Database and PHP server.
2. Install [Composer](https://getcomposer.org/doc/00-intro.md)
3. Install [npm](https://docs.npmjs.com/getting-started/installing-node). 
4. Install git. Get this project from Github (git clone).
5. Copy ".env.example" file and rename to ".env". Edit the .env file (connect to DB).
6. Run "composer install".
7. Run "npm install", then "npm run dev".
8. Run "php artisan key:generate". It will add application key to the .env file.
9. Run "php artisan migrate" [Laravel Migrations](https://laravel.com/docs/5.5/migrations).
10. !Importantly. Correct way for seeding: "php artisan db:seed --class=DatabaseSeeder" [Laravel Seeding](https://laravel.com/docs/5.5/seeding).
11. Setup "Document root" for your project on server like ".../my_example_shop/public".

Uladzimir Sadkou: hofirma@gmail.com