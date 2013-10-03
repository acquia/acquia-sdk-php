<?php

namespace Acquia\Network;

class Subscription extends \ArrayObject
{
    /**
     * @param string $networkId
     * @param string $networkKey
     * @param array $response
     *
     * @return \Acquia\Network\Subscription
     */
    public static function loadFromResponse($networkId, $networkKey, array $response)
    {
        $subscription = new static($response['body']);
        $subscription['id'] = $networkId;
        $subscription['key'] = $networkKey;
        return $subscription;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return !empty($this['active']);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this['id'];
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this['key'];
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this['uuid'];
    }

    /**
     * @return string
     */
    public function getDashboardUrl()
    {
        return $this['href'];
    }

    /**
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return new \DateTime($this['expiration_date']['value']);
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this['product']['view'];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUuid();
    }
}
