<?php

namespace Acquia\Environment;

class Environment implements EnvironmentInterface
{
    const PRODUCTION  = 'prod';
    const STAGING     = 'test';
    const DEVELOPMENT = 'dev';
    const LOCAL       = 'local';

    /**
     * @var string
     */
    private $environment;

    /**
     * Constructor, fires init() hook to initialize the environment.
     */
    public function __construct()
    {
        $environment = $this->init();
        $this->setEnvironment($environment);
    }

    /**
     * Calculates and returns the environment.
     *
     * @return string
     */
    protected function init()
    {
        return self::DEVELOPMENT;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Acquia\Environment\Environment
     */
    public function setEnvironment($environment)
    {
        if (!is_string($environment)) {
            throw new \UnexpectedValueException('Environment must be a string');
        }
        $this->environment = $environment;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * {@inheritDoc}
     */
    public function isProduction()
    {
        return self::PRODUCTION == $this->environment;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->getEnvironment();
    }
}
