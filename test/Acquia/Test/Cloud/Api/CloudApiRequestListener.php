<?php

namespace Acquia\Test\Cloud\Api;

use Guzzle\Common\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CloudApiRequestListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $url = '';

    public static function getSubscribedEvents()
    {
        return array(
            'client.create_request' => array('onRequest', 0),
        );
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function onRequest(Event $event)
    {
        $this->url = $event['request']->getUrl();
    }
}
