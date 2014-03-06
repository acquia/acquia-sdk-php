<?php

namespace Acquia\Cloud\Environment;

use Acquia\Environment\Environment;

/**
 * @see https://docs.acquia.com/cloud/configure/env-variable
 */
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

    /**
     * @return bool
     */
    public function isProduction()
    {
        return (bool) getenv('AH_PRODUCTION');
    }

    /**
     * @rturn string
     *
     * @throws \UnexpectedValueException
     */
    public function getSiteGroup()
    {
        $sitegroup = getenv('AH_SITE_GROUP');
        if (!$sitegroup) {
            throw new \UnexpectedValueException('Expecting environment variable AH_SITE_GROUP to be set');
        }
        return $sitegroup;
    }
}
