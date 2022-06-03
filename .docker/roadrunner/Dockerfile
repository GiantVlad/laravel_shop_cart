ARG RR_VERSION
ARG RR_IMAGE=spiralscout/roadrunner:${RR_VERSION}
ARG PHP_IMAGE_VERSION
ARG PHP_IMAGE=php:${PHP_IMAGE_VERSION}
FROM ${RR_IMAGE} as rr

FROM ${PHP_IMAGE}

ARG WWWUSER=1000
ARG WWWGROUP=1000
ARG TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
    && echo $TZ > /etc/timezone


RUN apk update && apk add --no-cache \
  vim \
  libzip-dev \
  unzip \
  bash

RUN apk update && apk add --no-cache \
  gnupg \
  git \
  curl \
  wget \
  ca-certificates \
  supervisor \
  libmemcached-dev \
  libpq-dev \
  libpng-dev \
  libwebp-dev \
  libmcrypt-dev \
  oniguruma-dev \
  libzip-dev zip unzip \
  libxml2 \
  procps

###########################################
# pdo_mysql
###########################################

RUN docker-php-ext-install pdo_mysql;

###########################################
# zip
###########################################

RUN docker-php-ext-configure zip && docker-php-ext-install zip;

###########################################
# mbstring
###########################################

RUN docker-php-ext-install mbstring;

###########################################
# GD
###########################################
RUN apk update && apk add --no-cache \
    libjpeg-turbo-dev \
    freetype-dev

RUN docker-php-ext-configure gd \
            --prefix=/usr \
            --with-jpeg \
            --with-webp \
            --with-freetype \
    && docker-php-ext-install gd;

###########################################
# OPcache
###########################################

ARG INSTALL_OPCACHE=true

RUN if [ ${INSTALL_OPCACHE} = true ]; then \
      docker-php-ext-install opcache; \
  fi

###########################################
# PHP Redis
###########################################

ARG INSTALL_PHPREDIS=true

RUN if [ ${INSTALL_PHPREDIS} = true ]; then \
      apk add --no-cache pcre-dev $PHPIZE_DEPS \
      && pecl install redis \
      && docker-php-ext-enable redis.so; \
  fi

###########################################
# PCNTL
###########################################

ARG INSTALL_PCNTL=true

RUN if [ ${INSTALL_PCNTL} = true ]; then \
      docker-php-ext-install pcntl; \
  fi

###########################################
# BCMath
###########################################

ARG INSTALL_BCMATH=true

RUN if [ ${INSTALL_BCMATH} = true ]; then \
      docker-php-ext-install bcmath; \
  fi

###########################################
# RDKAFKA
###########################################

ARG INSTALL_RDKAFKA=false

RUN if [ ${INSTALL_RDKAFKA} = true ]; then \
      apk add --no-cache librdkafka-dev \
      && pecl -q install -o -f rdkafka \
      && docker-php-ext-enable rdkafka; \
  fi

###########################################
# OpenSwoole/Swoole extension
###########################################

ARG INSTALL_SWOOLE=false
ARG SERVER=openswoole

RUN if [ ${INSTALL_SWOOLE} = true ]; then \
       libc-ares-dev \
      && pecl -q install -o -f -D 'enable-openssl="yes" enable-http2="yes" enable-swoole-curl="yes" enable-mysqlnd="yes" enable-cares="yes"' ${SERVER} \
      && docker-php-ext-enable ${SERVER}; \
    fi

###########################################################################
# Human Language and Character Encoding Support
###########################################################################

ARG INSTALL_INTL=true

RUN if [ ${INSTALL_INTL} = true ]; then \
      apk add --no-cache zlib-dev icu-dev g++ \
      && docker-php-ext-configure intl \
      && docker-php-ext-install intl; \
  fi

###########################################
# Memcached
###########################################

ARG INSTALL_MEMCACHED=true

RUN if [ ${INSTALL_MEMCACHED} = true ]; then \
      pecl -q install -o -f memcached && docker-php-ext-enable memcached; \
  fi

###########################################
# MySQL Client
###########################################

ARG INSTALL_MYSQL_CLIENT=true

RUN if [ ${INSTALL_MYSQL_CLIENT} = true ]; then \
      apk add --no-cache mysql-client; \
  fi

###########################################
# pdo_pgsql
###########################################

ARG INSTALL_PDO_PGSQL=false

RUN if [ ${INSTALL_PDO_PGSQL} = true ]; then \
      docker-php-ext-install pdo_pgsql; \
  fi

###########################################
# pgsql
###########################################

ARG INSTALL_PGSQL=false

RUN if [ ${INSTALL_PGSQL} = true ]; then \
      docker-php-ext-install pgsql; \
  fi

###########################################
# pgsql client and postgis
###########################################

ARG INSTALL_PG_CLIENT=false
ARG INSTALL_POSTGIS=false

RUN if [ ${INSTALL_PG_CLIENT} = true ]; then \
      . /etc/os-release \
      && echo "deb http://apt.postgresql.org/pub/repos/apt $VERSION_CODENAME-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
      && curl -sL https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add - \
      && apt-get install -yqq --no-install-recommends --show-progress postgresql-client-12 postgis; \
      if [ ${INSTALL_POSTGIS} = true ]; then \
        apt-get install -yqq --no-install-recommends --show-progress postgis; \
      fi; \
  fi

###########################################
# Laravel scheduler
###########################################

RUN if [ ${CONTAINER_MODE} = 'scheduler' ] || [ ${APP_WITH_SCHEDULER} = true ]; then \
      wget -q "https://github.com/aptible/supercronic/releases/download/v0.1.12/supercronic-linux-amd64" \
           -O /usr/bin/supercronic \
      && chmod +x /usr/bin/supercronic \
      && mkdir -p /etc/supercronic \
      && echo "*/1 * * * * su octane -c \"php ${ROOT}/artisan schedule:run --verbose --no-interaction\"" > /etc/supercronic/laravel; \
  fi

###########################################
RUN apk add $PHPIZE_DEPS libstdc++ linux-headers

#RUN curl -sSLf -o pickle.phar https://github.com/FriendsOfPHP/pickle/releases/latest/download/pickle.phar
#
#RUN CPPFLAGS="-Wno-maybe-uninitialized" php pickle.phar install grpc-1.46.3

#RUN docker-php-ext-enable grpc && php --ri grpc
#RUN pecl install grpc; \
#	docker-php-ext-enable grpc

RUN git clone -b v1.48.0 https://github.com/grpc/grpc \
    && cd grpc \
    && git submodule update --init --depth 1 \
    && EXTRA_DEFINES=GRPC_POSIX_FORK_ALLOW_PTHREAD_ATFORK make \
    && grpc_root="$(pwd)" \
    && cd src/php/ext/grpc \
    && phpize \
    && GRPC_LIB_SUBDIR=libs/opt ./configure --enable-grpc="${grpc_root}" \
    && make \
    && make install

RUN cd ../ && docker-php-ext-enable grpc
###########################################

RUN docker-php-ext-install sockets

RUN addgroup -g $WWWGROUP octane \
    && adduser -D -s /bin/bash  -G octane -u $WWWUSER octane

RUN docker-php-source delete \
    && rm -rf /var/cache/apk/* /tmp/* /var/tmp/*
    # && rm /var/log/lastlog /var/log/faillog


# Copy Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy RoadRunner
COPY --from=rr /usr/bin/rr /usr/bin/rr

# Copy RoadRunner config
COPY ./.docker/roadrunner/config/rr.yaml /etc/.rr.yaml

WORKDIR /app

#COPY composer.* ./

#RUN composer install \
#      --no-dev \
#      --no-interaction \
#      --prefer-dist \
#      --no-scripts \
#      --ignore-platform-reqs \
#      --optimize-autoloader \
#      --apcu-autoloader \
#      --ansi


COPY .docker/roadrunner/docker-healthcheck.sh /usr/local/bin/docker-healthcheck

RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=1s --timeout=10s --retries=30 CMD ["docker-healthcheck"]
COPY ./ /app
CMD ["/usr/bin/rr", "serve", "-d", "-c", "/etc/.rr.yaml"]
