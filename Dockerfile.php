FROM php:7.4-fpm-alpine

# Instala e ativa as extensões mysqli e pdo_mysql necessárias para a conexão de banco de dados do PHP
RUN docker-php-ext-install mysqli pdo_mysql && docker-php-ext-enable mysqli pdo_mysql

WORKDIR /var/www/html
