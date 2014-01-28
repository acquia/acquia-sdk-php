<?php

namespace Acquia\Cloud\Api\Response;

class Domain extends \Acquia\Common\Element
{
    /**
     * @param string
     */
    public function name()
    {
        return $this['name'];
    }
}
