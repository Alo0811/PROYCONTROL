FROM php:8.2-apache
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* && a2enmod mpm_prefork
COPY . /var/www/html/
RUN mkdir -p /var/www/html/uploads/credenciales && chmod -R 777 /var/www/html/uploads
CMD sh -c 'rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* && echo "Listen ${PORT:-80}" > /etc/apache2/ports.conf && sed -i "s/:80>/:${PORT:-80}>/g" /etc/apache2/sites-available/000-default.conf && apache2-foreground'
