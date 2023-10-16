# RevKeep Production Docker Container
# Use .devcontainer/Dockerfile for development
FROM node:16-bullseye AS node
FROM php:8.1-apache-bullseye AS revkeep-base

# Node.JS Setup
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /usr/local/bin/node /usr/local/bin/node
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm && mkdir /npm && chmod a+rwx /npm

# Add microsoft package repo for ODBC
RUN apt-get update && apt-get install -y gnupg \
	&& curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
	&& curl https://packages.microsoft.com/config/debian/11/prod.list > /etc/apt/sources.list.d/mssql-release.list \
	&& apt-get update

# Install dependencies
RUN ACCEPT_EULA=Y apt-get install -y \
	libfreetype6-dev \
	libgss3 \
	libicu-dev \
	libjpeg62-turbo-dev \
	libpng-dev \
	libpq-dev \
	libwebp-dev \
	libzip-dev \
	msodbcsql18 \
	mssql-tools18 \
	unzip \
	zip \
	odbcinst=2.3.7 \
	odbcinst1debian2=2.3.7 \
	unixodbc=2.3.7 \
	unixodbc-dev=2.3.7

# Add MSSQL Tools to path
ENV PATH="/opt/mssql-tools18/bin:${PATH}"

# PECL Install PDO MS SQL Server drive
RUN pecl install pdo_sqlsrv sqlsrv

# Enable apache mod_rewrite
RUN a2enmod rewrite

# Configure PHP extensions and enable
RUN docker-php-ext-configure intl \
	&& docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp \
	&& docker-php-ext-install gd intl pdo pdo_pgsql opcache zip \
	&& docker-php-ext-enable sqlsrv pdo_sqlsrv opcache

# STAGE 2
FROM revkeep-base AS revkeep-build

# Copy docker-specific php configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY ./docker/php.ini $PHP_INI_DIR/conf.d/local.ini

# Set directory as apache's webroot
WORKDIR /var/www/html

# Copy source code from build
COPY . .

# Create .env file from defaults -- disabled to allow setting at container level
COPY ./config/.env.default ./config/.env

# Set apache user as owner of working directory (webroot)
RUN chown -R www-data:www-data .

# Build front end assets
RUN npm install --cache /npm && npm run prod --cache /npm

# Run composer install based on environment
RUN php composer.phar install --prefer-dist --no-interaction --profile --no-ansi --no-scripts --optimize-autoloader

# Run /src/Console/Installer.php
RUN php composer.phar run post-install-cmd --no-interaction

# Restart apache to take all configuration changes.
# Not needed
# RUN service apache2 restart

# Symlink for improved static asset performance (not serving assets through php)
RUN php bin/cake.php plugin assets symlink

# Expose web server
EXPOSE 80

# Provide health check
HEALTHCHECK --interval=10m --timeout=30s --start-period=5s --retries=3 CMD [ "curl --fail http://localhost/app-health || exit 1" ]

# Post create command
ENTRYPOINT ["bash", "/var/www/html/postCreateCommand.sh"]
