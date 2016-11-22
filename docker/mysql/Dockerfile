FROM mysql:5.6
MAINTAINER Julian Waller <julian@tech.guildtv.co.uk>

ENV MYSQL_ROOT_PASSWORD nastaSubLocal

ADD additional.cnf /etc/mysql/conf.d/

ADD entrypoint/* /docker-entrypoint-initdb.d/

RUN chmod 644 /etc/mysql/conf.d/*

VOLUME /var/lib/mysql