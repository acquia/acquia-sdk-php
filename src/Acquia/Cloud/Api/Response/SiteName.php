<?php

namespace Acquia\Cloud\Api\Response;

class SiteName extends \Acquia\Rest\Element
{
    /**
     * @return string
     */
    public function name()
    {
        return $this['name'];
    }
}
