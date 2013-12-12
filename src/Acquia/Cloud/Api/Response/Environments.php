<?php

namespace Acquia\Cloud\Api\Response;

class Environments extends \ArrayObject
{
    /**
     * @param array $envs
     */
    public function __construct($envs)
    {
        foreach ($envs as $env) {
            $this[$env['name']] = new Environment($env);
        }
    }
}
