<?php

namespace Acquia\Cloud\Api\Response;

class Sites extends \ArrayObject
{
    /**
     * @param array $sites
     */
    public function __construct($sites)
    {
        foreach ($sites as $site) {
            $this[$site] = new Site($site);
        }
    }
}
