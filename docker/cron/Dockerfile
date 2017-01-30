FROM nasta-sub:base
MAINTAINER Julian Waller <julian@tech.guildtv.co.uk>

ADD crontab /etc/crontab
ADD start.sh /start.sh

RUN chmod 755 /etc/crontab
RUN touch /var/log/cron.log

RUN chmod +x /start.sh
CMD ["/start.sh"]