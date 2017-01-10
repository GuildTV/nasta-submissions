#!/bin/bash

php artisan migrate --database=test_mysql

DB_CONNECTION=test_mysql APP_ENV=test ./vendor/bin/phpunit $1
