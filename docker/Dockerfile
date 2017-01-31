FROM guildtv/website-base
MAINTAINER Julian Waller <julian@tech.guildtv.co.uk>

RUN apt-get update && \
  apt-get install -y poppler-utils && \
  rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/lib/apt/cache/*.deb

# add source
WORKDIR /src
ADD ./ /src/

RUN chown -R www-data:www-data storage

# composer
RUN php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php \
  && php composer-setup.php --install-dir=/usr/bin --filename="composer" \
  && php -r "unlink('composer-setup.php');"
