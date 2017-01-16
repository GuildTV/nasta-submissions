#!/bin/bash

# change user id to match local user
if [ -n "$UID" ]; then 
  usermod -u $UID www-data
fi

cd /src
php artisan queue:work "$@"