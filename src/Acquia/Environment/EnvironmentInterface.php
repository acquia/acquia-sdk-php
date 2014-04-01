<?php

namespace Acquia\Environment;

/**
 * Interface for classes the provide information as to what environment the
 * application is running in.
 */
interface EnvironmentInterface
{
    /**
     * Sets the environemnt identifier, e.g. "dev", "test", "prod", etc.
     *
     * @param string $environment
     *
     * @return \Acquia\Environment\EnvironmentInterface
     */
    public function setEnvironment($environment);

    /**
     * Returns the environemnt identifier, e.g. "dev", "test", "prod", etc.
     *
     * @return string
     */
    public function getEnvironment();

    /**
     * Returns whether the environment is being used for production.
     *
     * @return bool
     */
    public function isProduction();

    /**
     * Returns the environemnt identifier, e.g. "dev", "test", "prod", etc.
     *
     * @return string
     */
    public function __toString();
}
