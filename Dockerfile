FROM exozet/php-fpm:7.2

LABEL "com.github.actions.name"="RST-Checker"
LABEL "com.github.actions.description"="check *.rst files against given rules"
LABEL "com.github.actions.icon"="check"
LABEL "com.github.actions.color"="blue"

LABEL "repository"="http://github.com/oskarstark/rst-checker"
LABEL "homepage"="http://github.com/actions"
LABEL "maintainer"="Oskar Stark <oskarstark@googlemail.com>"

ENV APP_ENV=prod

ADD . /usr/src/app

RUN composer install

ADD entrypoint.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]