#!/bin/bash

php tests/application/bin/console server:stop --no-debug --env=prod --quiet
php tests/application/bin/console server:start --no-debug --env=prod 127.0.0.1:4242 -d tests/application/public

if [ ! -f vendor/bin/chromedriver ]; then
    LATEST_CHROMEDRIVER=$(wget -qO- https://chromedriver.storage.googleapis.com/LATEST_RELEASE)
    curl http://chromedriver.storage.googleapis.com/$LATEST_CHROMEDRIVER/chromedriver_linux64.zip > chromedriver.zip
    unzip chromedriver.zip && rm chromedriver.zip
    mv -f chromedriver vendor/bin/
fi

vendor/bin/chromedriver
