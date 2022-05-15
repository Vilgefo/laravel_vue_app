FROM php:8.1.1-apache as main

WORKDIR /var/www
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
RUN docker-php-ext-install -j$(nproc) pdo_mysql
RUN apt-get update && apt-get install -y \
    build-essential \
    locales \
    git \
    unzip \
    zip \
    curl \
    default-mysql-client
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -

# 3. mod_rewrite for URL rewrite and mod_headers for .htaccess extra headers like Access-Control-Allow-Origin-
RUN a2enmod rewrite headers
RUN apt-get update && \
    apt-get install -yq tzdata && \
    ln -fs /usr/share/zoneinfo/Europe/Moscow /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata

ENV TZ="Europe/Moscow"
RUN ln -snf /usr/share/zoneinfo/$tz /etc/localtime && echo $tz > /etc/timezone
RUN apt-get install -y nodejs
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www
RUN apt-get install zip
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY --chown=www:www ./ /usr/src/cache

WORKDIR /usr/src/cache
USER www
RUN composer install
RUN npm install

USER root
RUN chown -R www:www ./
WORKDIR /var/www
RUN chown -R www:www ./
CMD chmod ugo+x /usr/src/cache/entrypoint.sh && bash /usr/src/cache/entrypoint.sh
USER www