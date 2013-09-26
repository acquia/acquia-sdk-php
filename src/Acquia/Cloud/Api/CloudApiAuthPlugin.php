<?php

namespace Acquia\Cloud\Api;

use Guzzle\Common\Event;
use Guzzle\Http\Message\Request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Adds HTTP authentication for the Cloud API.
 */
class CloudApiAuthPlugin implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
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
     * Request before-send event handler.
     *
     * @param \Guzzle\Common\Event $event
     */
    public function onRequestBeforeSend(Event $event)
    {
        $this->setAuth($event['request']);
    }

    /**
     * This method seems silly, but it will be really useful if / when the
     * authentication scheme becomes more complex. Separating it out from the
     * event handler allows us to test this code more easily.
     *
     * @param \Guzzle\Http\Message\Request $request
     */
    public function setAuth(Request $request)
    {
        $request->setAuth($this->username, $this->password);
    }
}
