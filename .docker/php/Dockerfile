FROM php:8.3-fpm

# Update repo and install some utilites
RUN apt-get update && apt-get upgrade -y && apt-get install -y --no-install-recommends \
    htop \
    mc \
	acl \
	file \
	gettext \
	git \
	&& rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# https://github.com/mlocati/docker-php-extension-installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# PHP
RUN set -eux; \
	install-php-extensions \
        gd \
		apcu \
		intl \
		opcache \
		zip \
        xdebug \
        pcntl \
	;

# Cleanup
RUN set -eux; \
	rm -rf /tmp/*

WORKDIR /var/www/app
