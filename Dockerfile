FROM php:8.1-apache

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy backend (API)
COPY backend/ /var/www/backend/

# Copy frontend (static HTML/CSS/JS)
COPY frontend/public/ /var/www/html/

# Apache config to serve static files + proxy API to PHP
RUN echo '<VirtualHost *:8080>\n\
    DocumentRoot /var/www/html\n\
    DirectoryIndex index.html\n\
    \n\
    # Serve static frontend\n\
    <Directory /var/www/html>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    \n\
    # Route /api and /health to PHP backend\n\
    Alias /api /var/www/backend/app/index.php\n\
    Alias /health /var/www/backend/app/index.php\n\
    \n\
    <Directory /var/www/backend>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    \n\
    # PHP handles API routes\n\
    <FilesMatch "index\.php$">\n\
        SetHandler application/x-httpd-php\n\
    </FilesMatch>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Cloud Run uses port 8080
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf

EXPOSE 8080

CMD ["apache2-foreground"]
