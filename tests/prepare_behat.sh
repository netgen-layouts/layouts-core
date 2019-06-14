#!/bin/bash

php tests/application/bin/console server:stop --no-debug --env=prod --quiet
php tests/application/bin/console server:start --no-debug --env=prod 127.0.0.1:4242 -d tests/application/public

if [ ! -f vendor/bin/chromedriver ]; then
    # MAJOR_CHROME_VERSION=$(google-chrome --product-version | grep -o '^[0-9]\+')
    MAJOR_CHROME_VERSION=74
    LATEST_CHROMEDRIVER=$(wget -qO- https://chromedriver.storage.googleapis.com/LATEST_RELEASE_$MAJOR_CHROME_VERSION)
    curl http://chromedriver.storage.googleapis.com/$LATEST_CHROMEDRIVER/chromedriver_linux64.zip > chromedriver.zip
    unzip chromedriver.zip && rm chromedriver.zip
    mv -f chromedriver vendor/bin/
fi

vendor/bin/chromedriver
