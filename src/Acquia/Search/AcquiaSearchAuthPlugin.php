<?php

namespace Acquia\Search;

use Guzzle\Common\Event;
use Guzzle\Http\Message\EntityEnclosingRequest;
use Guzzle\Http\Message\Request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Guzzle plugin that adds Acquia Search credentials.
 */
class AcquiaSearchAuthPlugin implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $indexId;

    /**
     * @var string
     */
    protected $derivedKey;

    /**
     * @param string $indexId
     * @param string $derivedKey
     */
    public function __construct($indexId, $derivedKey)
    {
        $this->indexId = $indexId;
        $this->derivedKey = $derivedKey;
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
     * @return \Acquia\Search\AcquiaSearchPligin
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
     * @return string
     */
    public function getDerivedKey()
    {
        return $this->derivedKey;
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
        $signature = new Signature($this->derivedKey);

        $url = $request->getPath();
        if ('POST' == $request->getMethod() && $request instanceof EntityEnclosingRequest) {
            $body = (string) $request->getBody();
            $hash = $signature->generate($body);
        } else {
            $url .= '?' . $request->getQuery();
            $hash = $signature->generate($url);
        }

        $request->addCookie('acquia_solr_time', $signature->getRequestTime());
        $request->addCookie('acquia_solr_nonce', $signature->getNonce());
        $request->addCookie('acquia_solr_hmac', $hash . ';');
    }
}
