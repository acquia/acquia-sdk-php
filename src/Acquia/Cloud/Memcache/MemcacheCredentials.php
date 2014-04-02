<?php

namespace Acquia\Cloud\Memcache;

class MemcacheCredentials extends \ArrayObject
{
    /**
     * @return int
     */
    public function host()
    {
        return $this['host'];
    }

    /**
     * @return int
     */
    public function port()
    {
        return $this['port'];
    }

    /**
     * Returns the string stored in the config.json file.
     */
    public function __toString()
    {
        return $this->host() . ':' . $this->port();
    }
}
