# Overview

An unoffical PHP SDK for Acquia services.

NOTE: This project is **EXPERIMENTAL**

# Usage

Basic usage examples for the SDK.

## Acquia Network

```php

require_once 'vendor/autoload.php';

use Acquia\Network\Client\AcquiaNetworkClient;

$client = AcquiaNetworkClient::factory(array(
    'acquia_id' => 'XXXX-XXXXX',  // Acquia Network identifier
    'acquia_key' => 'xxxxxx...',  // Acquia Network key
));
$subscription = $client->checkSubscription();

// Prints the link to the subscription's Acquia Network dashboard.
echo $subscription->dashboardUrl();

```

## Acquia Search

Simple keyword search:

```php

$searchService = new \Acquia\Search\Service($subscription);
$acquiaSearch = $searchService->getClient();
$results = $acquiaSearch->select('my keywords');

```

Use the [service builder](http://guzzlephp.org/webservice-client/using-the-service-builder.html)
to instantiate an Acquia Search. This will avoid excessive calls to the Acquia
Network API:

```php

// Save the Acquia Search credentials to a JSON file.
file_put_contents('/path/to/acquia_search.json', $searchService);

```

```php

use Guzzle\Service\Builder\ServiceBuilder;

// Load the credentials and instantiate a client.
$builder = ServiceBuilder::factory('/path/to/acquia_search.json');
$acquiaSearch = $builder->get('XXXX-XXXXX');

```

More advanced search usage:

```php

// Build the Solr parameters manually.
// @see http://wiki.apache.org/solr/CommonQueryParameters
$params = array('q' => 'my keywords', 'rows' => 20);
$results = $acquiaSearch->select($params);

// Issue arbitrary Solr requests to Acquia Search.
// @see http://guzzlephp.org/http-client/request.html#get-requests
// @see http://lucene.apache.org/solr/
$results = $acquiaSearch->get('/solr/XXXX-XXXXX/admin/ping?wt=json')->send()->json();

```

# Installation

The Acquia SDK can be installed with [Composer](http://getcomposer.org) by
adding this library as a dependency.

```json
{
    "require": {
        "cpliakas/acquia-sdk-php": "dev-master"
    }
}

```
