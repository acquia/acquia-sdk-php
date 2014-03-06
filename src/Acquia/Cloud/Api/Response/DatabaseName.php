<?php

namespace Acquia\Cloud\Api\Response;

class DatabaseName extends \Acquia\Rest\Element
{
    /**
     * @return string
     */
    public function name()
    {
        return $this['name'];
    }
}
