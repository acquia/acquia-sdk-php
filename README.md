# Acquia SDK for PHP

[![Build Status](https://travis-ci.org/acquia/acquia-sdk-php.svg?branch=master)](https://travis-ci.org/acquia/acquia-sdk-php)
[![Code Coverage](https://scrutinizer-ci.com/g/acquia/acquia-sdk-php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/acquia/acquia-sdk-php/?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/acquia/acquia-sdk-php.svg)](http://hhvm.h4cc.de/package/acquia/acquia-sdk-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/acquia/acquia-sdk-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/acquia/acquia-sdk-php/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/acquia/acquia-sdk-php/v/stable.png)](https://packagist.org/packages/acquia/acquia-sdk-php)
[![License](https://poser.pugx.org/acquia/acquia-sdk-php/license.svg)](https://packagist.org/packages/acquia/acquia-sdk-php)

The Acquia SDK for PHP allows developers to [build](https://www.youtube.com/watch?v=8wDSj18sXbg)
applications on top of [Acquia](https://www.acquia.com/) services.

Acquia provides open cloud hosting, developer tools and world-class support for
Drupal, the open source content management platform that unifies content,
community and commerce.

## Installation

The SDK can be installed with [Composer](http://getcomposer.org) by adding this
library as a dependency to your composer.json file.

```json
{
    "require": {
        "acquia/acquia-sdk-php": "*"
    }
}
```

After running `php composer.phar update` on the command line, include the
autoloader in your PHP scripts so that the SDK classes are made available.

```php
require_once 'vendor/autoload.php';
```

#### Take Only What You Need

Instead of downloading the entire SDK, it is recommended to take only what you
need by requiring the individual components you intend to use. For example, the
following code requires the Acquia Search component and it's dependencies.

```json
{
    "require": {
        "acquia/acquia-sdk-php-search": "*"
    }
}
```

The following components are available:

* [acquia/acquia-sdk-php-cloud-api](https://packagist.org/packages/acquia/acquia-sdk-php-cloud-api) Cloud API client library
* [acquia/acquia-sdk-php-cloud-db](https://packagist.org/packages/acquia/acquia-sdk-php-cloud-db) Returns credentials for the active master database on Acquia Cloud
* [acquia/acquia-sdk-php-cloud-memcache](https://packagist.org/packages/acquia/acquia-sdk-php-cloud-memcache) Returns credentials for the memcache servers on Acquia Cloud
* [acquia/acquia-sdk-php-cloud-env](https://packagist.org/packages/acquia/acquia-sdk-php-cloud-env) Provides context about the Acquia Cloud environment
* [acquia/acquia-sdk-php-common](https://packagist.org/packages/acquia/acquia-sdk-php-common) SDK version and constants that denote Acquia Services
* [acquia/acquia-sdk-php-env](https://packagist.org/packages/acquia/acquia-sdk-php-env) Allows applications to add context about the environment they are running in
* [acquia/acquia-sdk-php-json](https://packagist.org/packages/acquia/acquia-sdk-php-json) Utility methods to work with JSON
* [acquia/acquia-sdk-php-network](https://packagist.org/packages/acquia/acquia-sdk-php-network) Acquia Network client library
* [acquia/acquia-sdk-php-rest](https://packagist.org/packages/acquia/acquia-sdk-php-rest) Utility methods for consuming REST-like APIs
* [acquia/acquia-sdk-php-search](https://packagist.org/packages/acquia/acquia-sdk-php-search) Acquia Search client library

## Usage

Basic usage examples for the SDK.

### Cloud API

The [Cloud API](https://docs.acquia.com/cloud/api) is a web service that that
developers can use to extend, enhance, and customize
[Acquia Cloud](https://www.acquia.com/products-services/acquia-cloud).

```php
use Acquia\Cloud\Api\CloudApiClient;

$cloudapi = CloudApiClient::factory(array(
    'username' => 'xxx...',  // Email address used to log into the Acquia Network
    'password' => 'xxx...',  // Acquia Network password
));

$sites = $cloudapi->sites();
```

### Acquia Network

The [Acquia Network](https://www.acquia.com/products-services/drupal-support-and-cloud-services)
is a comprehensive suite of tools to help you create and manage killer web
sites, backed by the best Drupal support team in the world.

```php
use Acquia\Network\AcquiaNetworkClient;
use Acquia\Common\Services;

$network = AcquiaNetworkClient::factory(array(
    'network_id' => 'XXXX-XXXXX',  // Acquia Network identifier
    'network_key' => 'xxxxxx...',  // Acquia Network key
));

// Enable Acquia Search and return index information.
$acquiaServices = Services::ACQUIA_SEARCH;

$subscription = $network->checkSubscription($acquiaServices);
print $subscription->getDashboardUrl();
```

### Acquia Search

[Acquia Search](https://www.acquia.com/products-services/acquia-network/cloud-services/acquia-search)
is a fully managed enterprise site search solution built on Apache Solr and
other open source technologies.

```php
use Acquia\Search\AcquiaSearchService;

// A subscription can have multiple indexes. The Acquia Search service builder
// generates credentials and clients for all of the subscription's indexes.
$search = AcquiaSearchService::factory($subscription);

$index = $search->get('XXXX-XXXXX');
$results = $index->select('my keywords');
```

Refer to the [PSolr](https://github.com/cpliakas/psolr) project's documentation
for more advanced usage examples.

*Recommended*: Use the Service Manager to store credentials so that you don't have
to query the Acquia Network on every search request.

### Acquia Cloud Database

The Database component allows developers to connect to the active master
database when running applications on [Acquia Cloud](https://www.acquia.com/products-services/acquia-cloud).

```php
use Acquia\Cloud\Database\DatabaseService;

$service = new DatabaseService();

$creds = $service->credentials('mydatabase');
$dbh = new PDO($creds, $creds->username(), $creds->password());

```

#### Local Development

The SDK facilitates code portability for developers who like to test their
application locally. The following snippet shows how to connect to a local
database.

```php
use Acquia\Cloud\Database\DatabaseService;
use Acquia\Cloud\Environment\LocalEnvironment;

// "mydatabase" is the name of the database on Acquia Cloud.
$environment = new LocalEnvironment('mysite');
$environment->addDatabaseCredentials('mydatabase', 'local_db_name', 'db_user', 'db_password');

$service = new DatabaseService($environment);

$creds = $service->credentials('mydatabase');
$dbh = new PDO($creds, $creds->username(), $creds->password());

```

### Acquia Cloud Memcache

The Memcache component allows developers to connect to the [Memcached](http://memcached.org/)
caching system when running applications on [Acquia Cloud](https://www.acquia.com/products-services/acquia-cloud).

```php
use Acquia\Cloud\Memcache\MemcacheService;

$service  = new MemcacheService();
$memcache = new \Memcache();

$creds = $service->credentials();
foreach ($creds as $server) {
    $memcache->addServer($server->host(), $server->port());
}

```

#### Local Development

The SDK facilitates code portability for developers who like to test their
application locally. The following snippet shows how to connect to a local
memcache server.

```php
use Acquia\Cloud\Memcache\MemcacheService;
use Acquia\Cloud\Environment\LocalEnvironment;

$environment = new LocalEnvironment('mysite');
$environment->addMemcacheCredentials('localhost', 11211);

$service  = new MemcacheService(environment);
$memcache = new \Memcache();

$creds = $service->credentials();
foreach ($creds as $server) {
    $memcache->addServer($server->host(), $server->port());
}

```

Refer to the [Memcache PECL](http://us2.php.net/manual/en/book.memcache.php)
project's documentation for more details.

## The Acquia Service Manager

The Acquia Service Manager simplifies credential management and client
instantiation. The credential management system is built using
[Guzzle's service builder](http://docs.guzzlephp.org/en/latest/webservice-client/using-the-service-builder.html)
subsystem, so the documentation and techniques can also apply here.

### Saving Credentials

The following example saves the configurations for the client to JSON files in
the specified directory. Note that the Acquia Search client is a service
builder which is why we use the `setBuilder` method for it.

```php
use Acquia\Rest\ServiceManager;

$services = new ServiceManager(array(
    'conf_dir' => '/path/to/conf/dir',
));

$services
    ->setClient('cloudapi', 'mysite', $cloudapi)
    ->setClient('network', 'XXXX-XXXXX', $network)
    ->setBuilder('search', $search)
;

$services->save();
```

### Instantiating Service Clients

Clients can now be instantiated from the service manager by passing the service
group (e.g. "network", "search", etc.) and service name defined in the
`setClient()` method. For Acquia Search, the service builder automatically
names the clients after their index identifiers.

```php

use Acquia\Rest\ServiceManager;

$services = new ServiceManager(array(
    'conf_dir' => '/path/to/conf/dir',
));

$cloudapi = $services->getClient('cloudapi', 'mysite');
$network = $services->getClient('network', 'XXXX-XXXXX');
$index = $services->getClient('search', 'XXXX-XXXXX');

```

## Contributing and Development

Submit changes using GitHub's standard [pull request](https://help.github.com/articles/using-pull-requests) workflow.

All code should adhere to the following standards:

* [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)
* [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
* [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)

It is recommend to use the [PHP Coding Standards Fixer](https://github.com/fabpot/PHP-CS-Fixer)
tool to ensure that code adheres to the coding standards mentioned above.

Refer to [PHP Project Starter's documentation](https://github.com/cpliakas/php-project-starter#using-apache-ant)
for the Apache Ant targets supported by this project.
