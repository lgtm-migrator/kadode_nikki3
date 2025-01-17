FROM php:8.1-fpm-buster
LABEL maintainer="ucan-lab <yes@u-can.pro>"
SHELL ["/bin/bash", "-oeux", "pipefail", "-c"]

# timezone environment
ENV TZ=UTC \
  # locale
  LANG=en_US.UTF-8 \
  LANGUAGE=en_US:en \
  LC_ALL=en_US.UTF-8 \
  # composer environment
  COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_HOME=/composer

COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

RUN apt-get update && \
  apt-get -y install git libicu-dev libonig-dev libzip-dev unzip locales python3-pip libmariadb-dev vim cpulimit htop cron default-mysql-client python3-venv  libgirepository1.0-dev  && \
  apt-get clean && \
  rm -rf /var/lib/apt/lists/* && \
  locale-gen en_US.UTF-8 && \
  localedef -f UTF-8 -i en_US en_US.UTF-8 && \
  mkdir /var/run/php-fpm && \
  docker-php-ext-install intl pdo_mysql zip bcmath && \
  composer config -g process-timeout 3600 && \
  composer config -g repos.packagist composer https://packagist.org && \
  pip3 install --upgrade pip && \
  pip3 install python-dotenv mysqlclient ahocorapy && \
  pip3 install -U ginza ja-ginza

RUN git clone https://github.com/phpredis/phpredis.git /usr/src/php/ext/redis && \
  docker-php-ext-install redis

COPY ./infra/dev/php/php-fpm.d/zzz-www.conf /usr/local/etc/php-fpm.d/zzz-www.conf
COPY ./infra/dev/php/php.ini /usr/local/etc/php/php.ini

WORKDIR /work/backend
