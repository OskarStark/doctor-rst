FROM php:7.4-cli-alpine

LABEL "com.github.actions.name"="DOCtor-RST"
LABEL "com.github.actions.description"="check *.rst files against given rules"
LABEL "com.github.actions.icon"="check"
LABEL "com.github.actions.color"="blue"

LABEL "repository"="http://github.com/oskarstark/doctor-rst"
LABEL "homepage"="http://github.com/actions"
LABEL "maintainer"="Oskar Stark <oskarstark@googlemail.com>"

ENV APP_ENV=prod

COPY --from=composer:1.9.3 /usr/bin/composer /usr/bin/composer

WORKDIR /usr/src/app

ADD . /usr/src/app

RUN composer install --classmap-authoritative --no-interaction

ENTRYPOINT ["/usr/src/app/entrypoint.sh"]
