<?php

namespace Acquia\Cloud\Api\Response;

class Domain extends \Acquia\Rest\Element
{
    /**
     * @return string
     */
    public function name()
    {
        return $this['name'];
    }
}
