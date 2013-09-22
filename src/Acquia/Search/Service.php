<?php

namespace Acquia\Search;

use Acquia\Network\Subscription;

class Service implements \ArrayAccess
{
    /**
     * @var \Acquia\Network\Subscription
     */
    protected $subscription;

    /**
     * @var boolean
     */
    protected static $https = true;

    /**
     * An array of index information keyed by index ID used to instantiate the
     * clients.
     *
     * @var array
     */
    protected $indexInfo = array();

    /**
     * An array of instantiated \Acquia\Search\Client\AcquiaSearchClient objects
     * keyed by index ID.
     *
     * @var array
     */
    protected $clients = array();

    /**
     * @param \Acquia\Network\Subscription $subscription
     *
     * @throws \UnexpectedValueException
     */
    public function __construct(Subscription $subscription)
    {
        if (!isset($subscription['derived_key_salt'])) {
            throw new \UnexpectedValueException('Derived key salt not found in subscription');
        }
        if (!isset($subscription['heartbeat_data']['search_service_colony'])) {
            throw new \UnexpectedValueException('Acquia Search hostname not found in subscription');
        }
        if (!isset($subscription['heartbeat_data']['search_cores'])) {
            throw new \UnexpectedValueException('Index data not found in subscription');
        }

        foreach ($subscription['heartbeat_data']['search_cores'] as $indexInfo) {
            $this->indexInfo[$indexInfo['core_id']] = array(
                'hostname' => $indexInfo['balancer'],
                'index_id' => $indexInfo['core_id'],
                'acquia_key' => $subscription->key(),
                'salt' => $subscription['derived_key_salt'],
            );
        }

        $this->subscription = $subscription;
    }

    /**
     * @param boolean $https
     *
     * @return \Acquia\Search\Service
     */
    public static function https($https = true)
    {
        self::$https = (bool) $https;
    }

    /**
     * @return string
     */
    public static function getProtocol()
    {
        return self::$https ? 'https://' : 'http://';
    }

    /**
     * @return array
     */
    public function getIndexInfo()
    {
        return $this->indexInfo;
    }

    /**
     * @return bool
     */
    public function enabled()
    {
        return !empty($this->subscription['search_service_enabled']);
    }

    /**
     * @return string
     */
    public function hostname()
    {
        return $this->subscription['heartbeat_data']['search_service_colony'];
    }

    /**
     * @return string
     */
    public function salt()
    {
        return $this->subscription['derived_key_salt'];
    }

    /**
     * @param string $indexId
     * @param string|null $hostname
     * @param string|null $acquiaKey
     * @param string|null $salt
     */
    public function setIndex($indexId, $hostname = null, $acquiaKey = null, $salt = null)
    {
        $this->indexInfo[$indexId] = array(
            'hostname' => $hostname ?: $this->hostname(),
            'index_id' => $indexId,
            'acquia_key' => $acquiaKey ?: $this->subscription->key(),
            'salt' => $salt ?: $this->salt(),
        );

        $this->unsetClient($indexId);
    }

    /**
     * @param string|null $indexId
     *   If null, uses the subscription ID.
     *
     * @return \Acquia\Search\Client\AcquiaSearchClient
     *
     * @throws \OutOfBoundsException
     */
    public function getClient($indexId = null)
    {
        $indexId = $indexId ?: $this->subscription->id();
        return $this[$indexId];
    }

    /**
     * @param string $indexId
     */
    public function unsetClient($indexId)
    {
        unset($this->clients[$indexId]);
    }

    /**
     * @param string $indexId
     */
    public function offsetExists($indexId)
    {
        return isset($this->indexInfo[$indexId]);
    }

    /**
     * @param string $indexId
     *
     * @return \Acquia\Search\Client\AcquiaSearchClient
     *
     * @throws \OutOfBoundsException
     */
    public function offsetGet($indexId)
    {
        if (!isset($this->clients[$indexId])) {

            if (!isset($this->indexInfo[$indexId])) {
                throw new \OutOfBoundsException('Index not defined: ' . $indexId);
            }

            $config = $this->getClientConfig($indexId);
            $this->clients[$indexId] = Client\AcquiaSearchClient::factory($config);
        }

        return $this->clients[$indexId];
    }

    /**
     * Returns array passed to the client's factory function. It is assumed that
     * $indexId exists.
     *
     * @param string $indexId
     *
     * @return array
     */
    protected function getClientConfig($indexId)
    {
        $config = $this->indexInfo[$indexId];
        $config['base_url'] = self::getProtocol() . $config['hostname'];
        unset($config['hostname']);
        return $config;
    }

    /**
     * @param string $indexId
     * @param \Acquia\Search\Client\AcquiaSearchClient $client
     *
     * @throws \UnexpectedValueException
     */
    public function offsetSet($indexId, $client)
    {
        if (!$client instanceof Client\AcquiaSearchClient) {
            throw new \UnexpectedValueException('Value must be an instance of \Acquia\Search\Client\AcquiaSearchClient');
        }

        $this->clients[$indexId] = $client;
        $this->indexInfo[$indexId] = array(
            'hostname' => parse_url($client->getConfig('base_url'), PHP_URL_HOST),
            'index_id' => $client->getConfig('index_id'),
            'acquia_key' => $client->getConfig('acquia_key'),
            'salt' => $client->getConfig('salt'),
        );
    }

    /**
     * @param string $indexId
     */
    public function offsetUnset($indexId)
    {
        unset($this->clients[$indexId], $this->indexInfo[$indexId]);
    }

    /**
     * Returns the service builder array.
     *
     * @return array
     *
     * @see http://guzzlephp.org/webservice-client/using-the-service-builder.html#creating-a-service-builder
     */
    public function serviceBuilderArray()
    {
        $services = array();

        foreach ($this->indexInfo as $indexId => $indexInfo) {
            $services[$indexId] = array(
                'class' => 'Acquia\Search\Client\AcquiaSearchClient',
                'params' => $this->getClientConfig($indexId),
            );
        }

        return array('services' => $services);
    }

    /**
     * Returns the service builder JSON.
     *
     * @return string
     *
     * @see http://guzzlephp.org/webservice-client/using-the-service-builder.html#sourcing-from-a-json-document
     */
    public function serviceBuilderJson()
    {
        $options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
        if (defined('JSON_PRETTY_PRINT')) {
            $options = $options | JSON_PRETTY_PRINT;
        }
        if (defined('JSON_UNESCAPED_SLASHES')) {
            $options = $options | JSON_UNESCAPED_SLASHES;
        }
        return json_encode($this->serviceBuilderArray(), $options);
    }

    /**
     * @see \Acquia\Search\Service::serviceBuilderJson()
     */
    public function __toString()
    {
        return $this->serviceBuilderJson();
    }
}
