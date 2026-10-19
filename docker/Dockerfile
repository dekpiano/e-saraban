FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        intl \
        gd \
        zip \
        mysqli \
        pdo_mysql \
        bcmath

# Configure PHP upload settings
RUN printf 'upload_max_filesize = 64M\npost_max_size = 64M\nmemory_limit = 256M\n' > /usr/local/etc/php/conf.d/uploads.ini

# Enable Apache mod_rewrite & mod_ssl
RUN a2enmod rewrite ssl

# Change Apache document root to CI4's public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configure SSL self-signed certificate
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/apache-selfsigned.key \
    -out /etc/ssl/certs/apache-selfsigned.crt \
    -subj "/C=TH/ST=NakhonSawan/L=NakhonSawan/O=SKJ/OU=e-Saraban/CN=localhost" \
    && sed -i 's!/etc/ssl/certs/ssl-cert-snakeoil.pem!/etc/ssl/certs/apache-selfsigned.crt!g' /etc/apache2/sites-available/default-ssl.conf \
    && sed -i 's!/etc/ssl/private/ssl-cert-snakeoil.key!/etc/ssl/private/apache-selfsigned.key!g' /etc/apache2/sites-available/default-ssl.conf \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/default-ssl.conf \
    && a2ensite default-ssl

# Configure Apache Directory permissions for mod_rewrite and CI4
RUN printf '<Directory /var/www/html/public>\n    Options Indexes FollowSymLinks\n    AllowOverride All\n    Require all granted\n    AcceptPathInfo On\n</Directory>\n' > /etc/apache2/conf-available/ci4.conf \
    && a2enconf ci4

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
