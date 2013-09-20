# Overview

An unoffical PHP SDK for Acquia services.

NOTE: This project is **EXPERIMENTAL**

# Usage

Basic usage examples for the SDK.

## Get Acquia Network Subscription Data

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

# Acquia Search

```php

$searchService = new \Acquia\Search\Service($subscription);
$acquiaSearch = $searchService->getClient();

// Do a quick search for keywords ...
$results = $acquiaSearch->select('my keywords');

// ... or build the Solr parameters manually.
// @see http://wiki.apache.org/solr/CommonQueryParameters
$params = array('q' => 'my keywords', 'rows' => 20);
$results = $acquiaSearch->select($params);

// Issue arbitrary requests to Solr.
// @see http://guzzlephp.org/http-client/request.html#get-requests
// @see http://lucene.apache.org/solr/
$results = $acquiaSearch->get('/solr/XXXX-XXXXX/admin/ping?wt=json')->send()->json();

```

# Installation

The Acquia SDK can be installed with [Composer](http://getcomposer.org) by
adding this package as a dependency.

```json
{
    "require": {
        "cpliakas/acquia-sdk-php": "dev-master"
    }
}

```
