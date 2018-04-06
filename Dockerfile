FROM composer:1.6
FROM php:7.2-fpm-alpine3.7

RUN apk add --no-cache \
		git

ENV APCU_VERSION 5.1.9
RUN set -xe \
	&& apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		postgresql-dev \
		zlib-dev \
		libpng-dev \
		libxml2-dev \
		imagemagick-dev \
		imagemagick \
		imagemagick-libs \
	&& docker-php-ext-install \
		intl \
		pdo \
		pdo_mysql \
		pdo_pgsql \
		zip \
		gd \
		soap \
		bcmath \
	&& pecl install \
		apcu-${APCU_VERSION} \
		imagick \
	&& docker-php-ext-enable pdo \
	&& docker-php-ext-enable pdo_mysql \
	&& docker-php-ext-enable pdo_pgsql \
	&& docker-php-ext-enable imagick \
	&& docker-php-ext-enable gd \
	&& docker-php-ext-enable soap \
	&& docker-php-ext-enable bcmath \
	&& docker-php-ext-enable --ini-name 20-apcu.ini apcu \
	&& docker-php-ext-enable --ini-name 05-opcache.ini opcache \
	&& runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)" \
	&& apk add --no-cache --virtual .php-phpexts-rundeps $runDeps \
	&& apk del .build-deps

COPY --from=0 /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

WORKDIR /srv/audiencehero
ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --classmap-authoritative

# Prevent the reinstallation of vendors at every changes in the source code
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress --no-suggest \
	&& composer clear-cache

RUN mkdir -p var/cache var/logs var/sessions \
	&& composer dump-autoload --classmap-authoritative --no-dev \
    && chown -R www-data var
