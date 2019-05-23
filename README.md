# DAO PHP SDK

[![Latest Version][ico-version]][link-packagist]
[![Latest Unstable Version][ico-unstable-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]

A PHP SDK for the [DAO API](http://www.dao.as).

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this library:

```bash
$ composer require setono/dao-php-sdk
```

This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

## Usage
Here is an example showing you how you can get the nearest pickup points.

**Notice** that this example uses two libraries that are not installed by default: The PSR 17 factory and the PSR18 HTTP client implementation.
If you don't have any preferences, you can install these two libraries: `$ composer require kriswallsmith/buzz nyholm/psr7`.

```php
<?php
use Buzz\Client\Curl;
use Nyholm\Psr7\Factory\Psr17Factory;
use Setono\DAO\Client\Client;

$psr17Factory = new Psr17Factory();
$httpClient = new Curl($psr17Factory);

$client = new Client($httpClient, $psr17Factory, $psr17Factory, 'INSERT CUSTOMER ID', 'INSERT PASSWORD');
$client->get('/DAOPakkeshop/FindPakkeshop.php', [
    'postnr' => '9000', // zip code
    'adresse' => 'Hansenvej 10', // address
    'antal' => 10, // number of results to return
]);
```

[ico-version]: https://poser.pugx.org/setono/dao-php-sdk/v/stable
[ico-unstable-version]: https://poser.pugx.org/setono/dao-php-sdk/v/unstable
[ico-license]: https://poser.pugx.org/setono/dao-php-sdk/license
[ico-travis]: https://travis-ci.com/Setono/dao-php-sdk.svg?branch=master
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Setono/dao-php-sdk.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/setono/dao-php-sdk
[link-travis]: https://travis-ci.com/Setono/dao-php-sdk
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/dao-php-sdk
