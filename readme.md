# Intervention Pinboard

[![Latest Version](https://img.shields.io/packagist/v/intervention/pinboard.svg)](https://packagist.org/packages/intervention/pinboard)

This packages provides a local command line interface to interact with your link collection at [pinboard.in](https://pinboard.in/). The main reason behind this project is, to have a local mirror of all Pinboard links an a Sqlite database to be able to search the whole collection quickly with [Alfred](https://www.alfredapp.com). Therefore this packages also provides an Alfred Workflow.

## Installation

The best way to install this package is globally with Composer.

Require the package via Composer:

    $ composer global require intervention/pinboard

After installation you will have a new `pinboard` executable in `~/.composer/vendor/bin`. It is convenient to have this folder in your `$PATH`.

## Setup

Next you have to configure your Pinboard credentials. To do so create `~/.pinboard` your home directory and put in your username and your access token.

```
PINBOARD_USERNAME=myusername
PINBOARD_TOKEN=mypinboardtoken
```

Now you can run the application, to sync your links from your Pinboard account.

    $ pinboard sync

You can automate the syncing process by calling the script regularly as a cron job.

## License

Intervention Pinboard is licensed under the [MIT License](http://opensource.org/licenses/MIT).

Copyright 2025 [Oliver Vogel](https://intervention.io/)
