<?php

namespace Acquia\Network;

use Acquia\Common\AcquiaServiceManagerAware;
use Acquia\Network\Subscription;
use Guzzle\Common\Collection;
use Guzzle\Service\Client;

class AcquiaNetworkClient extends Client implements AcquiaServiceManagerAware
{
    const NONCE_LENGTH = 55;

    /**
     * @var string
     */
    protected $networkId;

    /**
     * @var string
     */
    protected $networkKey;

    /**
     * {@inheritdoc}
     *
     * @return \Acquia\Network\AcquiaNetworkClient
     */
    public static function factory($config = array())
    {
        $required = array(
            'base_url',
            'network_id',
            'network_key',
        );

        $defaults = array(
            'base_url' => 'https://rpc.acquia.com',
        );

        // Instantiate the Acquia Search plugin.
        $config = Collection::fromConfig($config, $defaults, $required);
        return new static(
            $config->get('base_url'),
            $config->get('network_id'),
            $config->get('network_key'),
            $config
        );
    }

    /**
     * @param string $networkUri
     * @param string $networkId
     * @param string $networkKey
     * @param mixed $config
     */
    public function __construct($networkUri, $networkId, $networkKey, $config = null)
    {
        $this->networkId = $networkId;
        $this->networkKey = $networkKey;

        parent::__construct($networkUri, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function getBuilderParams()
    {
        return array(
            'base_url' => $this->getConfig('base_url'),
            'network_id' => $this->networkId,
            'network_key' => $this->networkKey,
        );
    }

    /**
     * @return string
     */
    public function getNetworkId()
    {
        return $this->networkId;
    }

    /**
     * @return string
     */
    public function getNetworkKey()
    {
        return $this->networkKey;
    }

    /**
     * @return \Acquia\Network\Subscription
     */
    public function checkSubscription()
    {
        $signature = new Signature($this->networkKey);
        $signature->getNoncer()->setLength(self::NONCE_LENGTH);

        $params = array(
            'authenticator' => array(
                'identifier' => $this->networkId,
                'time' => $signature->getRequestTime(),
                'hash' => $signature->generate(),
                'nonce' => $signature->getNonce(),
            ),
            'ssl' => isset($_SERVER['HTTPS']) ? 1 : 0,
            'ip' => isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '',
            'host' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '',
        );

        $response = $this->call('acquia.agent.subscription', $params);
        return Subscription::loadFromResponse($this->networkId, $this->networkKey, $response);
    }

    /**
     * @param string $method
     * @param array $params
     *
     * @return array
     *
     * @throws \fXmlRpc\Exception\ResponseException
     */
    public function call($method, array $params)
    {
        $uri = $this->getConfig('base_url') . '/xmlrpc.php';
        $bridge = new \fXmlRpc\Transport\GuzzleBridge($this);
        $client = new \fXmlRpc\Client($uri, $bridge);

        // We have to nest the params in an array otherwise we get a "Wrong
        // number of method parameters" error.
        return $client->call($method, array($params));
    }
}
