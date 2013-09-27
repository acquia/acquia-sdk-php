# Acquia SDK PHP

[![Build Status](https://travis-ci.org/cpliakas/acquia-sdk-php.png?branch=master)](https://travis-ci.org/cpliakas/acquia-sdk-php)
[![Coverage Status](https://coveralls.io/repos/cpliakas/acquia-sdk-php/badge.png?branch=master)](https://coveralls.io/r/cpliakas/acquia-sdk-php?branch=master)
[![Dependency Status](http://www.versioneye.com/user/projects/5244fbae632bac211a001f5d/badge.png)](http://www.versioneye.com/user/projects/5244fbae632bac211a001f5d)

An unoffical PHP SDK for Acquia services.

NOTE: This project is **EXPERIMENTAL**


## Installation

The SDK can be installed with [Composer](http://getcomposer.org) by adding this
library as a dependency to your composer.json file.

```json
{
    "require": {
        "cpliakas/acquia-sdk-php": "dev-master"
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

```php
use Acquia\Cloud\Api\CloudApiClient;

$cloudapi = CloudApiClient::factory(array(
    'username' => 'xxx...',
    'password' => 'xxx...',
));

$sites = $cloudapi->sites();
```

### Acquia Network

```php
use Acquia\Network\AcquiaNetworkClient;

$network = AcquiaNetworkClient::factory(array(
    'network_id' => 'XXXX-XXXXX',  // Acquia Network identifier
    'network_key' => 'xxxxxx...',  // Acquia Network key
));

$subscription = $network->checkSubscription();
print $subscription->getDashboardUrl();
```

### Acquia Search

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
[Guzzle's service builder](http://guzzlephp.org/webservice-client/using-the-service-builder.html)
subsystem, so the documentation and techniques can also apply here.

### Saving Credentials

The following example saves the configurations in the two clients and one
service builder (for Acquia Search) to JSON files in the specified directory.

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
