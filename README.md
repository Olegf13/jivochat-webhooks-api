# Jivochat Webhooks API

Library for [Jivochat](https://www.jivochat.com) ([Jivosite](https://www.jivosite.ru)) Webhooks API integration.

THIS LIBRARY IS CURRENTLY IN ACTIVE DEVELOPMENT STATE!

This library allows you to integrate with Jivosite Webhooks API and:
* handle API calls in event-based manner;
* convert API requests data in particular event objects;
* generate API responses;
* save original request (and generated response) data into MySQL or MongoDB server, and log it via Monolog.

For Russian documentation see [README-ru.md](README-ru.md).

## Installation

The preferred way to install this library is through [Composer](http://getcomposer.org/download/).

To install the latest version, run:

```
composer require olegf13/jivochat-webhooks-api
```

## Basic usage

```php
<?php

// todo: write me
```

## Documentation

* Jivochat Webhooks API [official documentation](https://www.jivochat.com/api/#webhooks)

## Requirements

The library requires PHP 7.0 or above for basic usage.

Optional requirements:
* [PDO extension](http://php.net/manual/en/book.pdo.php) allows logging of Webhooks request/response data to a MySQL server;
* [Monolog library](https://github.com/Seldaek/monolog) allows logging of Webhooks request/response using Monolog;
* [MongoDB library](https://github.com/mongodb/mongo-php-library) allows logging of Webhooks request/response data to a MongoDB server.

It is strongly recommended to have at least one of above loggers installed to hold a "backup" of original requests sent via Webhooks API.

## License

This library is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgements

Thanks to [this](https://github.com/nabarabane/jivosite) Jivosite Webhook handler library. 