# Intervention Pinboard

[![Latest Version](https://img.shields.io/packagist/v/intervention/pinboard.svg)](https://packagist.org/packages/intervention/pinboard)

This packages provides a local command line interface to interact with your
link collection at [pinboard.in](https://pinboard.in/). The main reason behind
this project is, to have a local mirror of all Pinboard links an a Sqlite
database to be able to search the whole collection quickly with the command
line.

## Installation

The best way to install this package is globally with Composer.

Require the package via Composer:

    $ composer global require intervention/pinboard

After installation you will have a new `pinboard` executable in
`~/.composer/vendor/bin`. It is convenient to have this folder in your `$PATH`.

## Setup

Next you have to configure your Pinboard credentials. To do so create
`~/.pinboard` your home directory and put in your username and your access
token.

```
PINBOARD_USERNAME=myusername
PINBOARD_TOKEN=mypinboardtoken
```

Now you can init the application, to create the internal sqlite database. This
only needs to be done once during the initial setup.

    $ pinboard init

## Usage

### Search Bookmarks

> $ pinboard search <keywords>

Search your bookmarks with the following command. The first time you run this
command, all your bookmarks will be loaded from your pinboard account and saved
locally. This happens once if no data is available or if the time interval for
a new update has expired.

### Add Bookmarks

> $ pinboard add <url> <tags>

Add a new bookmark to the collection, this will be saved both locally and in
your account at pinboard.in. If the URL already exists, the bookmark will not
be created again, but will be overwritten with the current data.

### See Status of Local Bookmark Database

> $ pinboard status

Show the status of the local database mirror of your Pinboard account.

### Load all Bookmarks from pinboard.in Account

> $ pinboard pull

Normally this command does not need to be called manually. It is executed
automatically when the local database exceeds an age specified by the config
value `PINBOARD_UPDATE_EVERY` in your `.pinboard` file. The default is `12 hours`.

## Authors

This library is developed and maintained by [Oliver Vogel](https://intervention.io)

## License

Intervention Pinboard is licensed under the [MIT License](LICENSE).
