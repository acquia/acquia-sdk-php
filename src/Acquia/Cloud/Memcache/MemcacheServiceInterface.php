<?php

namespace Acquia\Cloud\Memcache;

interface MemcacheServiceInterface
{
    /**
     * Returns the credentials memcache server.
     *
     * @throws \OutOfBoundsException
     *
     * @return \Acquia\Cloud\Memcache\MemcacheCredentials[]
     */
    public function credentials();
}
