#!/bin/sh

#set nginx to use correct number of processes
procs=$(cat /proc/cpuinfo |grep processor | wc -l)
sed -i -e "s/worker_processes 5/worker_processes $procs/" /etc/nginx/nginx.conf
sed -i -e "s/VM_ENV production/VM_ENV $VM_ENV/" /etc/nginx/sites-enabled/default

# change user id to match local user
if [ -n "$UID" ]; then 
  usermod -u $UID www-data
fi

cd /src

# ensure user has write access to bootstrap/cache
chown www-data:www-data bootstrap/cache -R

# run any database migrations
php artisan migrate

# remove caches when not in production, to allow debugbar and related to work
if [ "$APP_ENV" != "production" ]; then
  php artisan clear-compiled
  php artisan route:clear
fi

# Start supervisord and services
/usr/bin/supervisord -n
