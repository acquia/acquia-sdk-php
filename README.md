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
use Acquia\Network\Client\AcquiaNetworkClient;

$acquiaNetwork = AcquiaNetworkClient::factory(array(
    'network_id' => 'XXXX-XXXXX',  // Acquia Network identifier
    'network_key' => 'xxxxxx...',  // Acquia Network key
));
$subscription = $acquiaNetwork->checkSubscription();

print $subscription->getDashboardUrl();
```

### Acquia Search

#### Simple Keyword Search

```php
use Acquia\Search\AcquiaSearchService;

$acquiaSearch = AcquiaSearchService::factory($subscription);

$index = $acquiaSearch->get('XXXX-XXXXX');
$results = $index->select('my keywords');

// The code below is the equivalent to the two line snippet above.
$results = $acquiaSearch['XXXX-XXXXX']->select('my keywords');
```

#### Credential Management

Save the Acquia Search credentials in a JSON file in to avoid having to make
excessive Acquia Network API calls.

```php
$acquiaSearch->asJson('/path/to/acquia_search.json');
```

Load the service builder from the JSON file and instantiate a client.

```php
$acquiaSearch = AcquiaSearchService::factory('/path/to/acquia_search.json');
$results = $acquiaSearch['XXXX-XXXXX']->select('my keywords');
```

#### Advanced Solr Usage

```php
// Build the Solr parameters manually.
// @see http://wiki.apache.org/solr/CommonQueryParameters
$params = array('q' => 'my keywords', 'rows' => 20);
$results = $index->select($params);

// Issue arbitrary Solr requests to Acquia Search. The {base_path} expression is
// automatically expanded to "/solr/XXXX-XXXXX" for the index being queried.
// @see http://guzzlephp.org/http-client/request.html#get-requests
// @see http://lucene.apache.org/solr/
$results = $index->get('{base_path}/admin/ping?wt=json')->send()->json();
```
