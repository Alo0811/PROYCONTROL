FROM php:8.2-apache
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN a2dismod mpm_event mpm_worker || true && a2enmod mpm_prefork
COPY . /var/www/html/
RUN mkdir -p /var/www/html/uploads/credenciales && chmod -R 777 /var/www/html/uploads
CMD sed -i "s/80/${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && apache2-foreground
