FROM exozet/php-fpm:7.3

LABEL "com.github.actions.name"="*.rst Checker"
LABEL "com.github.actions.description"="check *.rst files for constraints"
LABEL "com.github.actions.icon"="check"
LABEL "com.github.actions.color"="blue"

LABEL "repository"="http://github.com/oskarstark/rst-checker"
LABEL "homepage"="http://github.com/actions"
LABEL "maintainer"="Oskar Stark <oskarstark@googlemail.com>"

ADD . /usr/src/app

ADD entrypoint.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]