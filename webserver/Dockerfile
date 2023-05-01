FROM php:8.1-apache

# Set Apache document root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Configure Apache to serve public/ directory
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

# Install PHP extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    IPE_GD_WITHOUTAVIF=1 install-php-extensions gd intl exif pdo_mysql

# Activate production php.ini and extensions
RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
RUN sed -ri -e 's!;extension=gd!extension=gd!g' $PHP_INI_DIR/php.ini
RUN sed -ri -e 's!;extension=intl!extension=intl!g' $PHP_INI_DIR/php.ini
RUN sed -ri -e 's!;extension=exif!extension=exif!g' $PHP_INI_DIR/php.ini
RUN sed -ri -e 's!;extension=pdo_mysql!extension=pdo_mysql!g' $PHP_INI_DIR/php.ini

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    echo export PATH="$HOME/.symfony5/bin:$PATH" >> ~/.bashrc

