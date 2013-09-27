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

        $serverAddress = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
        $httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $https = isset($_SERVER['HTTPS']) ? 1 : 0;

        $body = '<?xml version="1.0"?>
          <methodCall>
            <methodName>acquia.agent.subscription</methodName>
            <params>
              <param>
                <value>
                  <struct>
                    <member><name>authenticator</name>
                      <value>
                        <struct>
                          <member><name>identifier</name><value><string>' . $this->networkId . '</string></value></member>
                          <member><name>time</name><value><int>' . $signature->getRequestTime() . '</int></value></member>
                          <member><name>hash</name><value><string>' . $signature->generate() . '</string></value></member>
                          <member><name>nonce</name><value><string>' . $signature->getNonce() . '</string></value></member>
                        </struct>
                      </value>
                    </member>
                    <member><name>ip</name><value><string>' . $serverAddress . '</string></value></member>
                    <member><name>host</name><value><string>' . $httpHost . '</string></value></member>
                    <member><name>ssl</name><value><boolean>' . $https . '</boolean></value></member>
                    <member>
                      <name>body</name>
                      <value>
                        <struct>
                          <member><name>search_version</name>
                            <value>
                              <struct>
                              </struct>
                            </value>
                          </member>
                        </struct>
                      </value>
                    </member>
                  </struct>
                </value>
              </param>
            </params>
          </methodCall>'
        ;

        $xml = $this->post('xmlrpc.php', array(), $body)->send()->xml();
        $xmlrpcResponse = new XmlrpcResponse($xml);
        return Subscription::loadFromResponse($this->networkId, $this->networkKey, $xmlrpcResponse);
    }
}
