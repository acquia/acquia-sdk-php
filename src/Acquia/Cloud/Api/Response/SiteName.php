<?php

namespace Acquia\Cloud\Api\Response;

class SiteName extends Object
{
    /**
     * @return string
     */
    public function name()
    {
        return $this['name'];
    }
}
