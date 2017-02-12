#!/bin/bash

# change user id to match local user
if [ -n "$UID" ]; then 
  usermod -u $UID www-data
fi

touch /etc/crontab /etc/cron.*/*

rsyslogd
cron
touch /var/log/cron.log
tail -F /var/log/syslog /var/log/cron.log