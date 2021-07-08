# crawlSpotify

## Installation
```bash
git clone
composer install
```

I use spatie/brwsershot package for naviagation, load ajax request and click on select and buttons  ( https://github.com/spatie/browsershot )

## Requirements
This package requires node 7.6.0 or higher and the Puppeteer Node library.

##  MacOS you can install Puppeteer in your project via NPM:
```bash
npm install puppeteer
```
Or you could opt to just install it globally
```bash
npm install puppeteer --global
```
## Ubuntu server you can install the latest stable version of Chrome like this:
```bash
curl -sL https://deb.nodesource.com/setup_12.x | sudo -E bash -
sudo apt-get install -y nodejs gconf-service libasound2 libatk1.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 libgbm1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator1 libnss3 lsb-release xdg-utils wget libgbm-dev
sudo npm install --global --unsafe-perm puppeteer
sudo chmod -R o+rx /usr/lib/node_modules/puppeteer/.local-chromium
```
## Execute command
```bash
bin/console crawl:spotify
```
