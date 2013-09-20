<?php

namespace Acquia\Network;

class Subscription extends \ArrayObject
{
    const NOT_FOUND = 1000;
    const KEY_MISMATCH = 1100;
    const EXPIRED = 1200;
    const REPLAY_ATTACK = 1300;
    const KEY_NOT_FOUND = 1400;
    const MESSAGE_FUTURE = 1500;
    const MESSAGE_EXPIRED = 1600;
    const MESSAGE_INVALID = 1700;
    const VALIDATION_ERROR = 1800;
    const PROVISION_ERROR = 9000;

    /**
     * @param string $acquiaId
     * @param string $acquiaKey
     * @param ]Acquia\Network\Client\XmlrpcResponse
     *
     * @return \Acquia\Network\Subscription
     */
    public static function loadFromResponse($acquiaId, $acquiaKey, Client\XmlrpcResponse $xmlrpcResponse)
    {
        $subscription = new static($xmlrpcResponse['body']);
        $subscription['id'] = $acquiaId;
        $subscription['key'] = $acquiaKey;
        return $subscription;
    }

    /**
     * @return bool
     */
    public function active()
    {
        return !empty($this['active']);
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this['id'];
    }

    /**
     * @return string
     */
    public function key()
    {
        return $this['key'];
    }

    /**
     * @return string
     */
    public function uuid()
    {
        return $this['uuid'];
    }

    /**
     * @return string
     */
    public function dashboardUrl()
    {
        return $this['href'];
    }

    /**
     * @return \DateTime
     */
    public function expires()
    {
        return new \DateTime($this['expiration_date']['value']);
    }

    /**
     * @return string
     */
    public function product()
    {
        return $this['product']['view'];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->uuid();
    }
}
