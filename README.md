# Acquia SDK for PHP

[![Build Status](https://travis-ci.org/acquia/acquia-sdk-php.png?branch=master)](https://travis-ci.org/acquia/acquia-sdk-php)
[![Coverage Status](https://coveralls.io/repos/acquia/acquia-sdk-php/badge.png?branch=master)](https://coveralls.io/r/acquia/acquia-sdk-php?branch=master)
[![Latest Stable Version](https://poser.pugx.org/acquia/acquia-sdk-php/v/stable.png)](https://packagist.org/packages/acquia/acquia-sdk-php)

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
    "repositories": [
        {
            "type": "pear",
            "url": "pear.php.net"
        }
    ],
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

### Acquia Cloud Database

The Database component allows developers to connect to the active master
database when running applications on [Acquia Cloud](https://www.acquia.com/products-services/acquia-cloud).

```php
use Acquia\Cloud\Database\Database;

$database = new Database();
$creds = $database->credentials('mydatabase');

$dbh = new PDO($creds, $creds->username(), $creds->password());

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
use Acquia\Common\AcquiaServiceManager;

$services = new AcquiaServiceManager(array(
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

use Acquia\Common\AcquiaServiceManager;

$services = new AcquiaServiceManager(array(
    'conf_dir' => '/path/to/conf/dir',
));

$cloudapi = $services->getClient('cloudapi', 'mysite');
$network = $services->getClient('network', 'XXXX-XXXXX');
$index = $services->getClient('search', 'XXXX-XXXXX');

```

## For Developers

Refer to [PHP Project Starter's documentation](https://github.com/cpliakas/php-project-starter#using-apache-ant)
for the Apache Ant targets supported by this project.
