FROM alpine:3.15

RUN apk add --update \
    rabbitmq-c-dev \
    php8-fpm \
    php8-apcu \
    php8-ctype \
    php8-curl \
    php8-dom \
    php8-gd \
    php8-fileinfo \
    php8-iconv \
    php8-json \
    php8-intl \
    php8-mbstring \
    php8-opcache \
    php8-openssl \
    php8-pcntl \
    php8-pdo \
    php8-pdo_mysql \
    php8-pdo_sqlite \
    php8-posix \
    php8-mysqli \
    php8-xml \
    php8-zlib \
    php8-phar \
    php8-tokenizer \
    php8-session \
    php8-simplexml \
    php8-sqlite3 \
    php8-xdebug \
    php8-xmlreader \
    php8-xmlwriter \
    php8-zip \
    php8-redis \
    php8-bcmath \
    php8-imap \
    make \
    curl \
    git \
    supervisor

RUN ln -s /usr/bin/php8 /usr/bin/php

RUN apk add --no-cache bash

RUN rm -rf /var/cache/apk/* && rm -rf /tmp/* && \
    curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

# Configure supervisor
RUN mkdir -p /etc/supervisor.d/
COPY supervisor.ini /etc/supervisor.d/supervisord.ini

#ADD xdebug.ini  /etc/php8/conf.d/

ADD api.pool.conf /etc/php8/php-fpm.d/
COPY php.ini /etc/php8/php.ini

#CMD ["php-fpm8", "-F"]

ADD crontab.txt /crontab.txt
COPY entry.sh /entry.sh
RUN chmod 755 /entry.sh
RUN /usr/bin/crontab /crontab.txt

CMD ["/entry.sh"]

CMD ["supervisord", "-c", "/etc/supervisor.d/supervisord.ini"]

WORKDIR /var/www/api
EXPOSE 9000

