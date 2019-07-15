#!/bin/bash

if [ ! -f vendor/bin/symfony ]; then
    LATEST_SYMFONY_CLI=$(wget -qO- https://get.symfony.com/cli/LATEST)
    curl -L https://github.com/symfony/cli/releases/download/v$LATEST_SYMFONY_CLI/symfony_linux_amd64.gz > symfony.gz
    gunzip symfony.gz && chmod 755 symfony
    mv -f symfony vendor/bin/
fi

if [ ! -f vendor/bin/chromedriver ]; then
    # MAJOR_CHROME_VERSION=$(google-chrome --product-version | grep -o '^[0-9]\+')
    MAJOR_CHROME_VERSION=74
    LATEST_CHROMEDRIVER=$(wget -qO- https://chromedriver.storage.googleapis.com/LATEST_RELEASE_$MAJOR_CHROME_VERSION)
    curl http://chromedriver.storage.googleapis.com/$LATEST_CHROMEDRIVER/chromedriver_linux64.zip > chromedriver.zip
    unzip chromedriver.zip && rm chromedriver.zip
    mv -f chromedriver vendor/bin/
fi

vendor/bin/symfony server:ca:install
vendor/bin/symfony server:start --daemon --port=4242 --document-root=tests/application/public
vendor/bin/chromedriver
