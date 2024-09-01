# Use a imagem base PHP 8.1 com a plataforma Linux/AMD64
FROM --platform=linux/amd64 php:8.1-fpm

# Defina o diretório de trabalho
WORKDIR /var/www

# Copie composer.lock e composer.json para o diretório de trabalho
COPY composer.lock composer.json /var/www/

# Instale dependências de sistema e extensões necessárias
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev \
    libgd-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instale extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Instale o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Adicione o usuário para a aplicação Laravel
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# Copie o conteúdo da aplicação para o diretório de trabalho
COPY . /var/www

# Altere as permissões para o usuário www
RUN chown -R www:www /var/www

# Mude o usuário atual para www
USER www

# Exponha a porta 9000 e inicie o servidor PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]
