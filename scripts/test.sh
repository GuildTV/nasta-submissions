#!/bin/bash

php artisan migrate --database=test_mysql

./vendor/bin/phpunit $1
