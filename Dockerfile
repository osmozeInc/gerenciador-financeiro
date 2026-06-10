# usa a imagem oficial do php 8.2 com o servidor apache
FROM php:8.2-apache

# atualiza e baixa os pacotes do linux
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libicu-dev

# Extensões do PHP
RUN docker-php-ext-install pdo pdo_pgsql intl

# Habilitamos o mod_rewrite do Apache (tecnicamente essencial para as rotas do padrão MVC)
RUN a2enmod rewrite

# Muda a pasta raiz do servidor web para /var/www/html/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Libera o Apache para ler o nosso arquivo .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf