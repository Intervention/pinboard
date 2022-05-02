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

## Usage in Alfred 4

Now you have local database of all your links. To be able to search your links with Alfred, import the workflow `SearchPinboard.alfredworkflow` by double clicking or dragging it into the application settings.

You may need to alter the path to the php executable in the Script Filter node. Feel free to change the other settings of the workflow as you like.

Now you should be able to search your Pinboard links quickly with Alfred by typing `p` and your search string.

<img src="https://raw.githubusercontent.com/Intervention/pinboard/master/storage/images/sample.png" width="605" height="155">

## License

Intervention Eloquent Hashid is licensed under the [MIT License](http://opensource.org/licenses/MIT).

Copyright 2022 [Oliver Vogel](https://intervention.io/)
