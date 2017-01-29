FROM guildtv/website-base
MAINTAINER Julian Waller <julian@tech.guildtv.co.uk>

# add source
WORKDIR /src
ADD ./ /src/

RUN chown -R www-data:www-data storage

# composer
RUN php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php \
  && php composer-setup.php --install-dir=/usr/bin --filename="composer" \
  && php -r "unlink('composer-setup.php');"

ADD docker/cron/crontab /etc/crontab
ADD docker/cron/start.sh /start.sh

RUN chmod 755 /etc/crontab
RUN touch /var/log/cron.log

RUN chmod +x /start.sh
CMD ["/start.sh"]