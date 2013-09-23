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
     * @param string $networkKey
     * @param string $salt
     * @param \Acquia\Common\NoncerAbstract $noncer
     */
    public function __construct($indexId, $networkKey, $salt, NoncerAbstract $noncer)
    {
        $this->indexId = $indexId;
        $this->derivedKey = new DerivedKey($salt, $networkKey);
        $this->noncer = $noncer;
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
     * Request before-send event handler.
     *
     * @param \Guzzle\Common\Event $event
     */
    public function onRequestBeforeSend(Event $event)
    {
        if ($event['request']->getMethod() != 'HEAD') {
            $this->signRequest($event['request']);
        }
    }

    /**
     * @param \Guzzle\Http\Message\Request $request
     */
    public function signRequest(Request $request)
    {
        $requestTime = $this->getRequestTime();
        $signature = new Signature($this->derivedKey, $this->noncer);

        $url = $request->getPath();
        if ('POST' == $request->getMethod()) {
            $body = (string) $request->getPostFields();
            $hash = $signature->generate($this->indexId, $body, $requestTime);
        } else {
            $url .= '?' . $request->getQuery();
            $hash = $signature->generate($this->indexId, $url, $requestTime);
        }

        $request->addCookie('acquia_solr_time', $requestTime);
        $request->addCookie('acquia_solr_nonce', $signature->getNonce());
        $request->addCookie('acquia_solr_hmac', $hash . ';');
    }
}
