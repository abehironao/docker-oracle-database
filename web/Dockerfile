FROM php:8.3-fpm

RUN apt-get update && \
  apt-get install -y git \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev && \
  docker-php-ext-install gd bcmath

ENV LD_LIBRARY_PATH /usr/local/instantclient/
ENV TNS_ADMIN /usr/local/instantclient/

COPY ./web/instantclient-basic-linux.arm64-19.10.0.0.0dbru.zip /tmp
COPY ./web/instantclient-sdk-linux.arm64-19.10.0.0.0dbru.zip /tmp

RUN apt-get update && \
    apt-get install -y zip unzip libaio1 wget && \
    unzip /tmp/instantclient-basic-linux.arm64-19.10.0.0.0dbru.zip -d /usr/local/ && \
    unzip /tmp/instantclient-sdk-linux.arm64-19.10.0.0.0dbru.zip -d /usr/local/ && \
    ln -s /usr/local/instantclient_19_10 /usr/local/instantclient && \
    export LD_LIBRARY_PATH=/usr/local/instantclient && \
    docker-php-ext-configure oci8 --with-oci8=instantclient,/usr/local/instantclient && \
  docker-php-ext-install oci8 && \
  docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/usr/local/instantclient,19.10 && \
  docker-php-ext-install pdo_oci && \
    rm -rf /usr/local/*.zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer