ARG RR_VERSION=2024.2.0
ARG RR_IMAGE=ghcr.io/roadrunner-server/roadrunner:${RR_VERSION}

FROM ${RR_IMAGE} as rr
FROM gaintvlad/lara_shop_roadrunner:8.3-2024.2

ARG XDEBUG_VERSION=3.2.1
ARG WWWUSER=1000
ARG WWWGROUP=1000

RUN addgroup -g $WWWGROUP octane \
    && adduser -D -s /bin/bash  -G octane -u $WWWUSER octane \

RUN set -eux; \
	apk add --no-cache --virtual .build-deps $PHPIZE_DEPS; \
	pecl install xdebug-$XDEBUG_VERSION; \
	docker-php-ext-enable xdebug; \
	apk del .build-deps

#RUN echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
#    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
#    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/xdebug.ini \
#    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
#    && echo "xdebug.connect_timeout_ms=2000" >> /usr/local/etc/php/conf.d/xdebug.ini
#    && echo "xdebug.log=/var/www/html/xdebug/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini \
#    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini
#    && echo "xdebug.client_port=9000" >> /usr/local/etc/php/conf.d/xdebug.ini


# Copy Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy RoadRunner
COPY --from=rr /usr/bin/rr /usr/bin/rr

# Copy RoadRunner config
COPY ./.docker/roadrunner/config/rr.yaml /etc/.rr.yaml

WORKDIR /app

COPY .docker/roadrunner/docker-healthcheck.sh /usr/local/bin/docker-healthcheck

COPY ./ /app

#ARG COMPOSER_ALLOW_SUPERUSER=1
#RUN composer update

RUN composer install \
      --no-dev \
      --no-interaction \
      --prefer-dist \
      --no-scripts \
      --ignore-platform-reqs \
      --optimize-autoloader \
      --apcu-autoloader \
      --ansi

RUN composer dumpautoload

RUN chmod +x /usr/local/bin/docker-healthcheck

EXPOSE 80

HEALTHCHECK --interval=1s --timeout=10s --retries=30 CMD ["docker-healthcheck"]

CMD ["/usr/bin/rr", "serve", "-d", "-c", "/etc/.rr.yaml"]
