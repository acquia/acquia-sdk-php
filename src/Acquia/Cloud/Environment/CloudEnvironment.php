<?php

namespace Acquia\Cloud\Environment;

use Acquia\Common\Environment;

class CloudEnvironment extends Environment
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $environment = getenv('AH_SITE_ENVIRONMENT');
        return $environment ?: 'local';
    }
}
