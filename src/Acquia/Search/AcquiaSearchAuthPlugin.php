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
     * @var \Acquia\Search\Signature
     */
    protected $signature;

    /**
     * @param string $indexId
     * @param \Acquia\Search\Signature $signature
     */
    public function __construct($indexId, Signature $signature)
    {
        $this->indexId = $indexId;
        $this->signature = $signature;
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
        return $this->signature->getSecretKey();
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
        $url = $request->getPath();
        if ('POST' == $request->getMethod() && $request instanceof EntityEnclosingRequest) {
            $body = (string) $request->getBody();
            $hash = $this->signature->generate($body);
        } else {
            $url .= '?' . $request->getQuery();
            $hash = $this->signature->generate($url);
        }

        $request->addCookie('acquia_solr_time', $this->signature->getRequestTime());
        $request->addCookie('acquia_solr_nonce', $this->signature->getNonce());
        $request->addCookie('acquia_solr_hmac', $hash . ';');

        // The timestamp should be current for each request.
        $this->signature->unsetRequestTime();
    }
}
