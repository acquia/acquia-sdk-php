<?php

namespace Acquia\Search\Client;

use Acquia\Common\NoncerAbstract;
use Acquia\Common\RandomStringNoncer;
use Guzzle\Common\Event;
use Guzzle\Http\Message\Request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Guzzle plugin that adds Acquia Search credentials.
 */
class AcquiaSearchPlugin implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $indexId;

    /**
     * @var \Acquia\Search\Client\DerivedKey
     */
    protected $derivedKey;

    /**
     * @var \Acquia\Common\NoncerAbstract
     */
    protected $noncer;

    /**
     * @var int
     */
    protected $requestTime = 0;

    /**
     * @param string $indexId
     * @param string $acquiaKey
     * @param string $salt
     * @param \Acquia\Common\NoncerAbstract $noncer
     */
    public function __construct($indexId, $acquiaKey, $salt, NoncerAbstract $noncer = null)
    {
        $this->indexId = $indexId;
        $this->derivedKey = new DerivedKey($salt, $acquiaKey);
        $this->noncer = $noncer ?: new RandomStringNoncer();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'request.before_send' => array('onRequestBeforeSend', -1000)
        );
    }

    /**
     * @param string $indexId
     *
     * @return \Acquia\Search\Client\AcquiaSearchPligin
     */
    public function setIndexId($indexId)
    {
        $this->indexId = $indexId;
        return $this;
    }

    /**
     * @return string
     */
    public function getIndexId()
    {
        return $this->indexId;
    }

    /**
     * @return \Acquia\Search\Client\DerivedKey
     */
    public function getDerivedKey()
    {
        return $this->derivedKey;
    }

    /**
     * @return \Acquia\Common\NoncerAbstract
     */
    public function getNoncer()
    {
        return $this->noncer;
    }

    /**
     * @param int $requestTime
     *
     * @return \Acquia\Search\Client\AcquiaSearchPligin
     */
    public function setRequestTime($requestTime)
    {
        $this->requestTime = $requestTime;
        return $this;
    }

    /**
     * @return \Acquia\Search\Client\AcquiaSearchPligin
     */
    public function unsetRequestTime()
    {
        $this->requestTime = 0;
        return $this;
    }

    /**
     * @return int
     */
    public function getRequestTime()
    {
        return $this->requestTime ?: time();
    }

    /**
     * Request before-send event handler
     *
     * @param Event $event Event received
     *
     *
     */
    public function onRequestBeforeSend(Event $event)
    {
        // @todo No signature required for HEAD requests.
        $this->signRequest($event['request']);
    }

    /**
     * @param \Guzzle\Http\Message\Request $request
     */
    public function signRequest(Request $request)
    {
        $requestTime = $this->getRequestTime();
        $signature = new Signature($this->derivedKey, $this->noncer);

        $url = $request->getPath() . '?' . $request->getQuery();
        $hash = $signature->generate($this->indexId, $url, $requestTime);

        $request->addCookie('acquia_solr_time', $requestTime);
        $request->addCookie('acquia_solr_nonce', $signature->nonce());
        $request->addCookie('acquia_solr_hmac', $hash . ';');
    }
}
