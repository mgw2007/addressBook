Address Book Test 
========================

Steps
--------------
you have two options 

Option 1
---------
Using your platform 
>composer install

note: when composer install ask for parameters values take default values no change 

after install you can run test 
> ./vendor/bin/phpunit 

to start server 
>  ./bin/console server:start 

and project running port will be appear 

or 

Option 2
---------
Using Docker compose

Run
> docker-compose up -d 

then access docker container bash using command 
>docker exec -it -u www-data  address-book-php-fpm bash

and in the opening bash run 
> composer install

note: when composer install ask for parameters values take default values no change 

after install you can run test 
> ./vendor/bin/phpunit 

and open project in server use 
http://localhost:9090/
