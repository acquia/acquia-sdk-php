<?php

namespace Acquia\Cloud\Environment;

use Acquia\Environment\Environment;
use Acquia\Json\Json;

/**
 * @see https://docs.acquia.com/cloud/configure/env-variable
 */
class CloudEnvironment extends Environment implements CloudEnvironmentInterface
{
    /**
     * @var string
     */
    private $sitegroup;

    /**
     * @var string
     */
    private $filepath;

    /**
     * @var array
     */
    private $creds;

    /**
     * @var string
     */
    private $sitename;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $environment = getenv('AH_SITE_ENVIRONMENT');
        return $environment ?: self::LOCAL;
    }

    /**
     * {@inheritDoc}
     */
    public function isAcquia()
    {
        return $this->getEnvironment() != self::LOCAL;
    }

    /**
     * @return bool
     */
    public function isProduction()
    {
        return (bool) getenv('AH_PRODUCTION');
    }

    /**
     * @param string $sitegroup
     *
     * @return \Acquia\Cloud\Environment\CloudEnvironment
     */
    public function setSiteGroup($sitegroup)
    {
        $this->sitegroup = $sitegroup;
        return $this;
    }

    /**
     * @rturn string
     *
     * @throws \UnexpectedValueException
     */
    public function getSiteGroup()
    {
        if (!isset($this->sitegroup)) {
            $this->sitegroup = getenv('AH_SITE_GROUP');
            if (!$this->sitegroup) {
                throw new \UnexpectedValueException('Expecting environment variable AH_SITE_GROUP to be set');
            }
        }
        return $this->sitegroup;
    }

    /**
     * @param string $sitename
     *
     * @return \Acquia\Cloud\Environment\CloudEnvironment
     */
    public function setSiteName($sitename)
    {
        $this->sitename = $sitename;
        return $this;
    }

    /**
     * @rturn string
     *
     * @throws \UnexpectedValueException
     */
    public function getSiteName()
    {
        if (!isset($this->sitename)) {
            $this->sitename = getenv('AH_SITE_NAME');
            if (!$this->sitename) {
                throw new \UnexpectedValueException('Expecting environment variable AH_SITE_NAME to be set');
            }
        }
        return $this->sitename;
    }

    /**
     * @param string $filepath
     *
     * @return \Acquia\Cloud\Environment\CloudEnvironment
     */
    public function setCredentialsFilepath($filepath)
    {
        $this->filepath = $filepath;
        return $this;
    }

    /**
     * @return string
     */
    public function getCredentialsFilepath()
    {
        if (!isset($this->filepath)) {
            $settingsDir = $this->getSiteGroup() . '.' . $this->getEnvironment();
            $this->filepath = '/var/www/site-php/' . $settingsDir . '/creds.json';
        }
        return $this->filepath;
    }

    /**
     * @return array
     *
     * @throws \RuntimeException
     */
    public function serviceCredentials()
    {
        if (!isset($this->creds)) {
            $filepath = $this->getCredentialsFilepath();
            $this->creds = Json::parseFile($filepath);
        }
        return $this->creds;
    }
}
