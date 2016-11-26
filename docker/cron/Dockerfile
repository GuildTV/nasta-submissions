FROM registry.julus.uk/nasta/submissions:base
MAINTAINER Julian Waller <julian@tech.guildtv.co.uk>

ADD docker/cron/crontab /etc/crontab
ADD docker/cron/start.sh /start.sh

RUN chmod 755 /etc/crontab
RUN touch /var/log/cron.log

RUN chmod +x /start.sh
CMD ["/start.sh"]