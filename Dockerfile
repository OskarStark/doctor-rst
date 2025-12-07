FROM php:8.4-cli-alpine AS build

RUN apk add --no-cache git # required for box to detect the version
RUN apk add --no-cache icu-dev && docker-php-ext-install -j$(nproc) intl # related to https://github.com/box-project/box/issues/516

COPY --from=composer:2.9.2 /usr/bin/composer /usr/bin/composer

WORKDIR /usr/src/app
ADD . /usr/src/app

RUN composer install --classmap-authoritative --no-interaction --no-dev --optimize-autoloader

ADD https://github.com/humbug/box/releases/download/4.6.10/box.phar ./box.phar
RUN php box.phar compile

FROM php:8.4-cli-alpine

LABEL "com.github.actions.name"="DOCtor-RST"
LABEL "com.github.actions.description"="check *.rst files against given rules"
LABEL "com.github.actions.icon"="check"
LABEL "com.github.actions.color"="blue"

LABEL "repository"="http://github.com/oskarstark/doctor-rst"
LABEL "homepage"="http://github.com/actions"
LABEL "maintainer"="Oskar Stark <oskarstark@googlemail.com>"

COPY --from=build /usr/src/app/bin/doctor-rst.phar /usr/bin/doctor-rst
COPY entrypoint.sh /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
