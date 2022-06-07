FROM php:5.6-apache 
RUN docker-php-ext-install mysqli
COPY php.ini /usr/local/etc/php/conf.d/php.ini

# Set up php sendmail config
RUN echo "sendmail_path=sendmail -i -t" >> /usr/local/etc/php/conf.d/php.ini
