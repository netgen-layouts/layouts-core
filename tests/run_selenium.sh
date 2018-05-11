#!/bin/bash

php tests/application/bin/console server:stop
php tests/application/bin/console server:start 127.0.0.1:8000 -d tests/application/web

if [ ! -f vendor/bin/selenium.jar ]; then
    curl http://selenium-release.storage.googleapis.com/3.12/selenium-server-standalone-3.12.0.jar > vendor/bin/selenium.jar
fi

if [ ! -f vendor/bin/chromedriver ]; then
    LATEST_CHROMEDRIVER=$(wget -qO- https://chromedriver.storage.googleapis.com/LATEST_RELEASE)
    curl http://chromedriver.storage.googleapis.com/$LATEST_CHROMEDRIVER/chromedriver_linux64.zip > chromedriver.zip
    unzip chromedriver.zip && rm chromedriver.zip
    mv -f chromedriver vendor/bin/
fi

java -Dwebdriver.chrome.driver=vendor/bin/chromedriver -jar vendor/bin/selenium.jar
