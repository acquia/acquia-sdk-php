<?php

namespace Acquia\Network;

use Acquia\Common\Services;
use Acquia\Common\Version;
use Acquia\Network\Subscription;
use Acquia\Rest\ServiceManagerAware;
use fXmlRpc\Exception\ResponseException;
use Guzzle\Common\Collection;
use Guzzle\Service\Client;

class AcquiaNetworkClient extends Client implements ServiceManagerAware
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
     * @var string
     */
    protected $serverAddress;

    /**
     * @var string
     */
    protected $httpHost;

    /**
     * @var bool
     */
    protected $https;

    /**
     * {@inheritdoc}
     *
     * @return \Acquia\Network\AcquiaNetworkClient
     */
    public static function factory($config = array())
    {

        $defaults = array(
            'base_url'       => 'https://rpc.acquia.com',
            'server_address' => isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '',
            'http_host'      => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '',
            'https'          => false,
            'network_id'     => '',
            'network_key'    => '',
        );

        // Instantiate the Acquia Search plugin.
        $config = Collection::fromConfig($config, $defaults);
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
        $this->serverAddress = $config->get('server_address');
        $this->httpHost = $config->get('http_host');
        $this->https = $config->get('https');

        parent::__construct($networkUri, $config);
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
     * {@inheritdoc}
     */
    public function getBuilderParams()
    {
        return array(
            'base_url'    => $this->getConfig('base_url'),
            'network_id'  => $this->networkId,
            'network_key' => $this->networkKey,
        );
    }

    /**
     * Returns default paramaters for request. Not every call requires these.
     *
     * @return array
     */
    protected function defaultRequestParams()
    {
        $params = array(
            'authenticator' => $this->buildAuthenticator(),
            'ssl'           => $this->https === true ? 1 : 0,
            'ip'            => $this->serverAddress,
            'host'          => $this->httpHost,
        );
        return $params;
    }

    /**
     * @param string $method
     * @param array $params
     *
     * @return array
     *
     * @throws \fXmlRpc\Exception\ResponseException
     */
    protected function call($method, array $params)
    {
        $uri = $this->getConfig('base_url') . '/xmlrpc.php';
        $bridge = new \fXmlRpc\Transport\GuzzleBridge($this);
        $client = new \fXmlRpc\Client($uri, $bridge);

        // We have to nest the params in an array otherwise we get a "Wrong
        // number of method parameters" error.
        return $client->call($method, array($params));
    }

    /**
     * @param string &$errstr
     *
     * @return bool
     */
    public function validateCredentials(&$errstr = null)
    {
        try {
            $errstr = '';
            $params = $this->defaultRequestParams();
            $this->call('acquia.agent.validate', $params);
            return true;
        } catch (ResponseException $e) {
            $errstr = $e->getMessage();
            return false;
        }
    }

    /**
     * @return string
     *
     * @throws \UnexpectedValueException
     * @throws \OutOfBoundsException
     */
    public function getSubscriptionName()
    {
        if (empty($this->networkId)) {
            throw new \UnexpectedValueException('Acquia Network identifier required');
        }

        $params = $this->defaultRequestParams();
        $params['body'] = array(
            'identifier' => $this->networkId,
        );

        $response = $this->call('acquia.agent.subscription.name', $params);
        if (!isset($response['body']['subscription']['site_name'])) {
            throw new \OutOfBoundsException('Invalid response returned from server.');
        }

        return $response['body']['subscription']['site_name'];
    }

    /**
     * @param string $email
     *
     * @return array
     *
     * @throws \fXmlRpc\Exception\ResponseException
     */
    public function getCommunicationSettings($email)
    {
        // Build a light authenticator.
        $signature = new Signature('x');
        $signature->getNoncer()->setLength(self::NONCE_LENGTH);
        $authentiator = array(
            'time' => $signature->getRequestTime(),
            'hash' => $signature->generate(),
            'nonce' => $signature->getNonce(),
        );
        $params = array(
            'authenticator' => $authentiator,
            'body' => array('email' => $email),
        );

        return $this->call('acquia.agent.communication.settings', $params);
    }

    /**
     * @param $email
     * @param $password
     *
     * @throws \fXmlRpc\Exception\ResponseException
     */
    public function getSubscriptionCredentials($email, $password)
    {
        // Build a light authenticator.
        $signature = new Signature($password);
        $signature->getNoncer()->setLength(self::NONCE_LENGTH);
        $authentiator = array(
            'time' => $signature->getRequestTime(),
            'hash' => $signature->generate(),
            'nonce' => $signature->getNonce(),
        );
        $params = array(
            'authenticator' => $authentiator,
            'body' => array('email' => $email),
        );

        $response = $this->call('acquia.agent.subscription.credentials', $params);
        // @todo catch error and/or check response is_error
        // @todo set this key/id
        return $response;
    }

    /*
     * @params array
     *   Parameters to have signed.
     *
     * @return string
     */
    protected function buildAuthenticator($params = array())
    {
        $signature = new Signature($this->networkKey);
        $signature->getNoncer()->setLength(self::NONCE_LENGTH);

        $authenticator = array(
            'identifier' => $this->networkId,
            'time' => $signature->getRequestTime(),
            'hash' => $signature->generate($params),
            'nonce' => $signature->getNonce(),
        );

        return $authenticator;
    }

    /**
     * @param array $options
     *   - search_version: An array of search module versions keyed by name.
     *   - no_heartbeat: Pass 1 to not send a heartbeat.
     *
     * @return \Acquia\Network\Subscription
     */
    public function getSubscription(array $options = array())
    {
        $params = $this->defaultRequestParams();
        $params['body'] = $options;

        $response = $this->call('acquia.agent.subscription', $params);
        return Subscription::loadFromResponse($this->networkId, $this->networkKey, $response);
    }

    /**
     * @param int $services
     *
     * @return \Acquia\Network\Subscription
     */
    public function checkSubscription($services = 0)
    {
        $options = array();

        if ($services & Services::ACQUIA_SEARCH) {
            $options = array(
                'search_version' => array('acquia/acquia-search-sdk' => Version::RELEASE)
            );
        }

        return $this->getSubscription($options);
    }

    /**
     * @return boolean
     */
    public function subscriptionActive()
    {
        $subscription = $this->getSubscription(array('no_heartbeat' => 1));
        return $subscription->isActive();
    }
}
