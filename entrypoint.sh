#!/bin/sh -l

sh -c "pwd -P"

sh -c "ls -la"

sh -c "php /usr/src/app/bin/console check $DOCS_DIR"
