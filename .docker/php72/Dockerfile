FROM php:7.2.3-fpm

RUN apt-get update \
    && apt-get install -y apt-transport-https apt-utils \
    && apt-get install -y --no-install-recommends vim debconf locales acl mailutils zip gnupg gnupg1 gnupg2 git wget libicu-dev g++ zlib1g-dev unzip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
        intl \
        opcache \
        pdo \
        pdo_mysql \
        zip

RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

COPY php-cli.ini /usr/local/etc/php/php.ini
COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY php-fpm-pool.conf /usr/local/etc/php/pool.d/www.conf

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

RUN wget https://get.symfony.com/cli/installer -O - | bash && mv ~/.symfony/bin/symfony /usr/local/bin/symfony

# NodeJS
RUN curl -sL https://deb.nodesource.com/setup_13.x | bash -
RUN apt-get install -y nodejs

# Yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt update && apt install yarn

# On créé un utilisateur avec le même gid/uid que votre local
# cela va permettre que les fichiers qui sont créés dans le contenaire auront vos droits
RUN addgroup --system raphael --gid 1000 && adduser --system raphael --uid 1000 --ingroup raphael

WORKDIR /var/www/

EXPOSE 9000
CMD ["php-fpm"]
