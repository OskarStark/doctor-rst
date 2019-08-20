#!/bin/sh -l

sh -c "php /usr/src/app/bin/console analyse $DOCS_DIR $*"
