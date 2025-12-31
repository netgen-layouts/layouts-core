#!/bin/bash

if [ ! -f vendor/bin/symfony ]; then
    wget -O symfony.tar.gz https://github.com/symfony-cli/symfony-cli/releases/latest/download/symfony-cli_linux_amd64.tar.gz
    tar xvf symfony.tar.gz --directory vendor/bin/ && rm symfony.tar.gz && chmod 755 vendor/bin/symfony
fi

if [ ! -f vendor/bin/chromedriver ]; then
    MAJOR_CHROME_VERSION=$(google-chrome --product-version | grep -o '^[0-9]\+')
    LATEST_CHROMEDRIVER=$(wget -qO- https://googlechromelabs.github.io/chrome-for-testing/LATEST_RELEASE_$MAJOR_CHROME_VERSION)
    wget -O chromedriver.zip https://edgedl.me.gvt1.com/edgedl/chrome/chrome-for-testing/$LATEST_CHROMEDRIVER/linux64/chromedriver-linux64.zip
    unzip chromedriver.zip && rm chromedriver.zip
    mv -f chromedriver-linux64/chromedriver vendor/bin/
    rm -rf chromedriver-linux64
fi

vendor/bin/symfony server:start --no-tls --daemon --port=4242 --document-root=tests/application/public
vendor/bin/chromedriver --port=9515
