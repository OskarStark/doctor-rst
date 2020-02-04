FROM exozet/php-fpm:7.4

LABEL "com.github.actions.name"="DOCtor-RST"
LABEL "com.github.actions.description"="check *.rst files against given rules"
LABEL "com.github.actions.icon"="check"
LABEL "com.github.actions.color"="blue"

LABEL "repository"="http://github.com/oskarstark/doctor-rst"
LABEL "homepage"="http://github.com/actions"
LABEL "maintainer"="Oskar Stark <oskarstark@googlemail.com>"

ENV APP_ENV=prod

ADD . /usr/src/app

RUN composer install

ENTRYPOINT ["/usr/src/app/entrypoint.sh"]
