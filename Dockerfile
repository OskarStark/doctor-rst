FROM php:7.4-cli-alpine as build

RUN apk add git # required for box to detect the version

COPY --from=composer:1.9.3 /usr/bin/composer /usr/bin/composer

WORKDIR /usr/src/app
ADD . /usr/src/app

RUN composer install --classmap-authoritative --no-interaction

ADD https://github.com/humbug/box/releases/download/3.8.4/box.phar ./box.phar
RUN php box.phar compile

FROM php:7.4-cli-alpine

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
