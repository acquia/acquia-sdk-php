<?php

namespace Acquia\Cloud\Api\Response;

class Servers extends \ArrayObject
{
    /**
     * @param array $servers
     */
    public function __construct($servers)
    {
        foreach ($servers as $server) {
            $this[$server['name']] = new Server($server);
        }
    }
}
