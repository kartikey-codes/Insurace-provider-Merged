# [Choice] PHP version (use -bullseye variants on local arm64/Apple Silicon): 8, 8.1, 8.0, 7, 7.4, 7.3, 8-bullseye, 8.1-bullseye, 8.0-bullseye, 7-bullseye, 7.4-bullseye, 7.3-bullseye, 8-buster, 8.1-buster, 8.0-buster, 7-buster, 7.4-buster
ARG VARIANT="8.1-apache-bullseye"
ARG NODE_VERSION="18"
ARG APACHE_DOCUMENT_ROOT="/workspaces/revkeep"
FROM mcr.microsoft.com/vscode/devcontainers/php:0-${VARIANT}

# Add node.js
RUN if [ "${NODE_VERSION}" != "none" ]; then su vscode -c "umask 0002 && . /usr/local/share/nvm/nvm.sh && nvm install ${NODE_VERSION} 2>&1"; fi

# Add microsoft package repo for ODBC
RUN apt-get update && apt-get install -y gnupg \
	&& curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
	&& curl https://packages.microsoft.com/config/debian/11/prod.list > /etc/apt/sources.list.d/mssql-release.list \
	&& apt-get update

# Install dependencies
RUN ACCEPT_EULA=Y apt-get install -y \
	libgss3 \
	libicu-dev \
	libpq-dev \
	libzip-dev \
	msodbcsql18 \
	mssql-tools18 \
	odbcinst \
	unixodbc \
	unixodbc-dev \
	unzip

# Add MSSQL Tools to path
ENV PATH="/opt/mssql-tools18/bin:${PATH}"

# PECL Install PDO MS SQL Server drive
RUN pecl install pdo_sqlsrv sqlsrv

# Enable apache mod_rewrite
RUN a2enmod rewrite

# Configure PHP extensions and enable
RUN docker-php-ext-configure intl \
	&& docker-php-ext-install intl pdo pdo_pgsql opcache zip \
	&& docker-php-ext-enable sqlsrv pdo_sqlsrv

# Copy docker-specific php configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY ./docker/php.ini $PHP_INI_DIR/conf.d/local.ini

# Copy source code from build
#COPY . .

# Create .env file from defaults -- disabled to allow setting at container level
#COPY ../config/.env.default config/.env

# Copy docker specific app configuration
#COPY ../docker/app/app_local.docker.php ./config/app_local.example.php

# Set apache user as owner of working directory (webroot)
#RUN chown -R www-data:www-data .

# Run /src/Console/Installer.php
#RUN php $WORKSPACE_DIR/composer.phar run post-install-cmd --no-interaction

# Change apache document root
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Restart apache to take all configuration changes.
RUN service apache2 restart

# Expose web server
EXPOSE 80
