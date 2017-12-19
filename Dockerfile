FROM php:7.1

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update -yqq && apt-get install -yqq apt-transport-https
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list

RUN curl -sL https://deb.nodesource.com/setup_6.x | bash -

RUN apt-get update -y && \
    apt-get install -y apt-transport-https && \
    apt-get install -y apt-utils && \
    apt-get install -y awscli && \
    apt-get install -y sudo wget yarn nodejs git libmagickwand-dev libmagickcore-dev libcurl4-gnutls-dev libicu-dev \
                       libmcrypt-dev libvpx-dev libjpeg-dev libpng-dev libxpm-dev zlib1g-dev libfreetype6-dev \
                       libxml2-dev libexpat1-dev libbz2-dev libgmp3-dev libldap2-dev unixodbc-dev libpq-dev \
                       libsqlite3-dev libaspell-dev libsnmp-dev libpcre3-dev libtidy-dev librabbitmq-dev librabbitmq1 \
                       unzip


RUN pecl install redis-3.1.1 && \
    pecl install imagick-3.4.3 && \
    pecl install xdebug-2.5.1 && \
    pecl install amqp-1.9.0 && \
    docker-php-ext-enable redis && \
    docker-php-ext-enable imagick && \
    docker-php-ext-enable xdebug && \
    docker-php-ext-enable amqp && \
    docker-php-ext-install bcmath mbstring mcrypt pdo pdo_pgsql curl json intl gd xml zip bz2 opcache soap pcntl

##
# Install composer
#
RUN wget https://composer.github.io/installer.sig -O - -q | tr -d '\n' > installer.sig && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('SHA384', 'composer-setup.php') === file_get_contents('installer.sig')) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php'); unlink('installer.sig');"

##
# At this point Google Chrome Beta is 59 - first version with headless support
#
RUN wget -q https://dl.google.com/linux/direct/google-chrome-beta_current_amd64.deb
RUN dpkg -i google-chrome-beta_current_amd64.deb; apt-get -fy install

##
# Install chromedriver to make it work with Selenium
#
RUN wget -q https://chromedriver.storage.googleapis.com/2.29/chromedriver_linux64.zip
RUN unzip chromedriver_linux64.zip -d /usr/local/bin

RUN apt-get clean
