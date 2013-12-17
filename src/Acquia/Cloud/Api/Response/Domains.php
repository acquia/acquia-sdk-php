<?php

namespace Acquia\Cloud\Api\Response;

class Domains extends \ArrayObject
{
    /**
     * @param array $dbs
     */
    public function __construct($domains)
    {
        foreach ($domains as $domain) {
            $this[$domain['name']] = new Domain($domain);
        }
    }
}
