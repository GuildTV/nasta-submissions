#!/bin/bash

php artisan migrate --database=test_mysql

APP_DEBUG=false DB_CONNECTION=test_mysql APP_ENV=test ./vendor/bin/phpunit $1
