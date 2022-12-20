<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

## This is a Laravel + Vue.js e-commerce template that will make it easy for you to start your own online store.


There is the [DEMO](http://uls.northeurope.cloudapp.azure.com/) ULE-shop.

[Video presentation](https://youtu.be/McmVr2FEo-0)

<p><img src="https://preview.ibb.co/dyyGMb/sshot_shop.png" alt="sshot_shop" border="0"></p>

1. Setup docker and docker-compose on your local machine.
2. Create the .local_data folder in the root directory of the project
3. Install git. Fetch this project from Github (git clone).
4. Copy ".env.example" file and rename to ".env". Edit the .env file (connect to DB).
5. Run "docker-compose up" (you may need to restart docker-compose up 3-4 times).



### Local endpoints:
- localhost  -- Main App
- localhost:8088 -- [Temporal](https://temporal.io) UI 

reset roadrunner server
```
docker-compose exec roadrunner rr -c /etc/.rr.yaml reset
```

How to use [xdebug with roadrunner](https://roadrunner.dev/docs/php-debugging/2.x/en) 


It uses roadrunner, Laravel/Octane and temporal.
If you prefer more traditional nginx/php server, checkout to the version 0.01 please.

_Uladzimir Sadkou_: hofirma@gmail.com 
