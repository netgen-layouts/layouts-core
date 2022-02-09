#!/bin/bash

if [ ! -f vendor/bin/symfony ]; then
    curl -L https://github.com/symfony-cli/symfony-cli/releases/latest/download/symfony-cli_linux_amd64.tar.gz > symfony.tar.gz
    tar xvf symfony.tar.gz --directory vendor/bin/ && rm symfony.tar.gz && chmod 755 vendor/bin/symfony
fi

if [ ! -f vendor/bin/chromedriver ]; then
    MAJOR_CHROME_VERSION=$(google-chrome --product-version | grep -o '^[0-9]\+')
    LATEST_CHROMEDRIVER=$(wget -qO- https://chromedriver.storage.googleapis.com/LATEST_RELEASE_$MAJOR_CHROME_VERSION)
    curl https://chromedriver.storage.googleapis.com/$LATEST_CHROMEDRIVER/chromedriver_linux64.zip > chromedriver.zip
    unzip chromedriver.zip && rm chromedriver.zip
    mv -f chromedriver vendor/bin/
fi

vendor/bin/symfony server:start --no-tls --daemon --port=4242 --document-root=tests/application/public
vendor/bin/chromedriver
