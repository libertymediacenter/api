FROM php:7.2-fpm

ARG INSTALL_DIR="/opt"

# Replace shell with bash so we can source files
RUN rm /bin/sh && ln -s /bin/bash /bin/sh

# Install developer dependencies
RUN set -xe \
    && apt-get update \
    && apt-get install -y -q --no-install-recommends \
        apt-transport-https \
        bison \
        build-essential \
        ca-certificates \
        cron \
        curl \
        haproxy \
        libbz2-dev \
        libcurl4-gnutls-dev \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libmagickwand-dev \
        libmcrypt-dev \
        libpng-dev \
        libpq-dev \
        libssl-dev \
        locales \
        python \
        rsync \
        software-properties-common \
        ssh-import-id \
        supervisor \
        telnet
        wget \
        zlib1g-dev \
    && docker-php-ext-install pdo \
        pdo_pgsql \
        opcache \
        calendar \
        bcmath \
        zip \
        bz2 \
        intl \
       # GD extension
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd \
       # PECL extensions
    && pecl install xdebug \
    && pecl install mcrypt-1.0.1 \
    && pecl install imagick \
    && pecl install redis \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-enable mcrypt \
    && docker-php-ext-enable imagick

#      && rm -rf /var/lib/apt/lists/*

COPY docker/php/xdebug.conf /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY docker/php/php.dev.ini /usr/local/etc/php/conf.d/php.dev.ini
COPY docker/php/php-debug /usr/local/bin/phpxdbg
COPY docker/php/sf-debug /usr/local/bin/sfdbg
RUN chmod 755 /usr/local/bin/phpxdbg && chmod 755 /usr/local/bin/sfdbg

COPY docker/php/php.run.sh /var/php.run.sh
RUN chmod 0777 /var/php.run.sh

WORKDIR $INSTALL_DIR
ENTRYPOINT "/var/php.run.sh"
