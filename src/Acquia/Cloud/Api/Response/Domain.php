<?php

namespace Acquia\Cloud\Api\Response;

class Domain extends Object
{
    /**
     * @param string
     */
    public function name()
    {
        return $this['name'];
    }
}
