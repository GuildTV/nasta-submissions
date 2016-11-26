FROM registry.julus.uk/nasta/submissions:base
MAINTAINER Julian Waller <julian@tech.guildtv.co.uk>

# Supervisor Config
COPY docker/http/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
ADD docker/http/start.sh /start.sh
RUN chmod +x /start.sh

ADD docker/http/nginx.conf /etc/nginx/sites-enabled/default

EXPOSE 80
CMD ["/start.sh"]