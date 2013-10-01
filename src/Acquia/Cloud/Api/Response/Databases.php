<?php

namespace Acquia\Cloud\Api\Response;

class Databases extends \ArrayObject
{
    /**
     * @param array $dbs
     */
    public function __construct($dbs)
    {
        foreach ($dbs as $db) {
            $this[$db['name']] = new Database($dbs);
        }
    }
}
