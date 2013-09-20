<?php

namespace Acquia\Network\Client;

use Acquia\Common\NoncerAbstract;
use Acquia\Common\RandomStringNoncer;
use Acquia\Network\Subscription;
use Guzzle\Common\Collection;
use Guzzle\Service\Client;

class AcquiaNetworkClient extends Client
{
    const NONCE_LENGTH = 55;

    /**
     * @var string
     */
    protected $acquiaId;

    /**
     * @var string
     */
    protected $acquiaKey;

    /**
     * @var \Acquia\Common\NoncerAbstract
     */
    protected $noncer;

    /**
     * {@inheritdoc}
     *
     * @return \Acquia\Network\Client\AcquiaNetworkClient
     */
    public static function factory($config = array())
    {
        $defaults = array(
            'base_url' => 'https://rpc.acquia.com',
            'noncer' => null,
        );

        $required = array(
            'base_url',
            'acquia_id',
            'acquia_key',
            'noncer',
        );

        // Instantiate the Acquia Search plugin.
        $config = Collection::fromConfig($config, $defaults, $required);
        return new static(
            $config->get('base_url'),
            $config->get('acquia_id'),
            $config->get('acquia_key'),
            $config->get('noncer'),
            $config
        );
    }

    /**
     *
     */
    public function __construct($baseUrl, $acquiaId, $acquiaKey, NoncerAbstract $noncer = null, $config = null)
    {
        if ($noncer === null) {
            $noncer = new RandomStringNoncer(self::NONCE_LENGTH);
        }

        $this->acquiaId = $acquiaId;
        $this->acquiaKey = $acquiaKey;
        $this->noncer = $noncer;

        parent::__construct($baseUrl, $config);
    }

    /**
     * @return string
     */
    public function getAcquiaId()
    {
        return $this->acquiaId;
    }

    /**
     * @return string
     */
    public function getAcquiaKey()
    {
        return $this->acquiaKey;
    }

    /**
     * @return \Acquia\Common\NoncerAbstract
     */
    public function getNoncer()
    {
        return $this->noncer;
    }

    /**
     * @return \Acquia\Network\Subscription
     */
    public function checkSubscription()
    {
        $signature = new Signature($this->acquiaId, $this->acquiaKey, $this->noncer);

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
                          <member><name>identifier</name><value><string>' . $this->acquiaId . '</string></value></member>
                          <member><name>time</name><value><int>' . $signature->getRequestTime() . '</int></value></member>
                          <member><name>hash</name><value><string>' . $signature->generate() . '</string></value></member>
                          <member><name>nonce</name><value><string>' . $signature->nonce() . '</string></value></member>
                        </struct>
                      </value>
                    </member>
                    <member><name>ip</name><value><string>127.0.0.1</string></value></member>
                    <member><name>host</name><value><string>apachesolrissues.dev</string></value></member>
                    <member><name>ssl</name><value><boolean>0</boolean></value></member>
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
        return Subscription::loadFromResponse($this->acquiaId, $this->acquiaKey, new Response($xml));
    }
}
