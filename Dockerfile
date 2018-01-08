FROM php:7.1-apache
MAINTAINER staciedev <a.borenko@gmail.com>

RUN apt-get update && apt-get install -y apt-transport-https
RUN apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 2930ADAE8CAF5059EE73BB4B58712A2291FA4AD5
RUN echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu xenial/mongodb-org/3.6 multiverse" | tee /etc/apt/sources.list.d/mongodb-org-3.6.list
RUN apt-get -qq update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng12-dev \
    libmemcached-dev \
    zlib1g-dev \
    libltdl7 \
    libltdl-dev \
    libpq-dev \
    libsqlite3-dev \
    git \
    curl \
    glibc \
    libcurl3-dev \
    rsyslog \
    cron \
    supervisor \
    unzip \
    libicu-dev \
    --no-install-recommends \
    && docker-php-ext-install -j$(nproc) iconv mcrypt pdo_mysql pdo_pgsql pdo_sqlite zip curl\
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

RUN apt-get update && apt-get install -y mongodb-org
RUN pecl install apcu memcached mongodb redis \
	  && docker-php-ext-enable apcu memcached mongodb redis

WORKDIR /var/www/html
COPY . .
RUN mv /var/www/html/application/core/config-example.php /var/www/html/application/core/config.php
RUN mv /var/www/html/mongodb.service /etc/systemd/system/mongodb.service

RUN mkdir -p /var/lock/apache2 /var/run/apache2 /var/log/supervisor

RUN ln -sf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime
RUN echo "date.timezone = Asia/Shanghai" >> /etc/php.ini

#composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/bin --filename=composer
RUN composer install

RUN service mongod start
RUN systemctl start mongodb
#apache
RUN a2enmod ssl rewrite
RUN { \
    echo '<VirtualHost *:80>';\
    	echo 'ServerAdmin webmaster@localhost';\
    	echo 'DocumentRoot /var/www/html';\
    	echo 'ErrorLog ${APACHE_LOG_DIR}/error.log';\
    	echo 'CustomLog ${APACHE_LOG_DIR}/access.log combined';\
    	echo 'SetEnv HTTPS ${FORCE_HTTPS}';\
    echo '</VirtualHost>';\
} > /etc/apache2/sites-available/000-default.conf
ENV HTTPS off
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN echo "export FORCE_HTTPS=\${HTTPS}" >> /etc/apache2/envvars