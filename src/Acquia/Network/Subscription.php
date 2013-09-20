<?php

namespace Acquia\Network;

class Subscription extends \ArrayObject
{
    /**
     * @param string $acquiaId
     * @param string $acquiaKey
     * @param Acquia\Network\Client\Reponse
     *
     * @return Acquia\Network\Subscription
     *
     */
    public static function loadFromResponse($acquiaId, $acquiaKey, Client\Response $response)
    {
        $subscription = new static($response['body']);
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
    public function derivedKeySalt()
    {
        return $this['derived_key_salt'];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->uuid();
    }
}
