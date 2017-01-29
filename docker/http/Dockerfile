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

# Supervisor Config
COPY docker/http/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
ADD docker/http/start.sh /start.sh
RUN chmod +x /start.sh

ADD docker/http/nginx.conf /etc/nginx/sites-enabled/default

EXPOSE 80
CMD ["/start.sh"]