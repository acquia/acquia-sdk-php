# Acquia SDK PHP

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

### Cloud API

```php
use Acquia\Cloud\Api\CloudApiClient;

$cloudapi = CloudApiClient::factory(array(
    'username' => 'xxx...',
    'password' => 'xxx...',
));

$sites = $cloudapi->sites();
```

### Acquia Search

#### Basic Usage

```php
use Acquia\Search\AcquiaSearchService;

$search = AcquiaSearchService::factory($subscription);

$index = $search->get('XXXX-XXXXX');
$results = $index->select('my keywords');
```

#### Advanced Solr Usage

```php
// Build the Solr parameters manually.
// @see http://wiki.apache.org/solr/CommonQueryParameters
$params = array('q' => 'my keywords', 'rows' => 20);
$results = $index->select($params);

// Issue arbitrary Solr requests to Acquia Search. The {+base_path} expression
// is automatically expanded to "/solr/XXXX-XXXXX" for the index being queried.
// @see http://guzzlephp.org/http-client/request.html#get-requests
// @see http://lucene.apache.org/solr/
$results = $index->get('{+base_path}/admin/ping?wt=json')->send()->json();
```

