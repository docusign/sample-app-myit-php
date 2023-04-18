FROM --platform=linux/amd64 php:8.1-fpm

RUN apt-get update && apt-get install -y \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
        git \
		libzip-dev \
    && docker-php-ext-install pdo_mysql mysqli zip  \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd \
	&& rm -rf /var/cache/apt/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.3.1

COPY --chown=www-data:www-data . /var/www/html

WORKDIR /var/www/html

RUN chmod -R 755 storage
RUN chmod -R 755 public/temp

RUN composer install --ignore-platform-reqs --no-dev

RUN php artisan cache:clear
