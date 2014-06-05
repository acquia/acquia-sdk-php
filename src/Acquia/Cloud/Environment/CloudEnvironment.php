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
     * Acquia Cloud variables may be set in settings.inc after PHP init,
     * so make sure that we are loading them.
     *
     * @param string $key
     * @return string The value of the environment variable or false if not found
     * @see https://github.com/acquia/acquia-sdk-php/pull/58#issuecomment-45167451
     */
    protected function getenv($key)
    {
        $value = getenv($key);
        if ($value === false) {
            if (isset($_ENV[$key])) {
                $value = $_ENV[$key];
            }
            if (isset($_SERVER[$key])) {
                $value = $_SERVER[$key];
            }
        }
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $environment = $this->getenv('AH_SITE_ENVIRONMENT');
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
        return (bool) $this->getenv('AH_PRODUCTION');
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
            $this->sitegroup = $this->getenv('AH_SITE_GROUP');
            if (!$this->sitegroup) {
                throw new \UnexpectedValueException('Expecting environment variable AH_SITE_GROUP to be set');
            }
        }
        return $this->sitegroup;
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
