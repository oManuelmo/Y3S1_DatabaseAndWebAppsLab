FROM ubuntu:24.04

# Install dependencies
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update; \
    apt-get install -y \
    cron \
    curl \
    git \
    unzip \
    libpq-dev \
    vim \
    nginx \
    php8.3-fpm \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-pgsql \
    php8.3-curl \
    ca-certificates

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy project code and install project dependencies
COPY --chown=www-data . /var/www/

# Copy project configurations
COPY ./etc/php/php.ini /usr/local/etc/php/conf.d/php.ini
COPY ./etc/nginx/default.conf /etc/nginx/sites-enabled/default
COPY .env.bidtano /var/www/.env
COPY docker_run.sh /docker_run.sh

# Start command
CMD sh /docker_run.sh
