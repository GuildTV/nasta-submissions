FROM nasta-sub:base
MAINTAINER Julian Waller <julian@tech.guildtv.co.uk>

# Supervisor Config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
ADD start.sh /start.sh
RUN chmod +x /start.sh

ADD nginx.conf /etc/nginx/sites-enabled/default

EXPOSE 80
CMD ["/start.sh"]