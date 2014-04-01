<?php

namespace Acquia\Cloud\Environment;

interface CloudEnvironmentInterface
{
    /**
     * Returns an associative array
     * @return array
     *
     * @throws \RuntimeException
     */
    public function serviceCredentials();

    /**
     * Sets the application's site group. Generally this value is detected at
     * runtime, so this method is most useful for testing purposes.
     *
     * @param string $sitegroup
     *
     * @return \Acquia\Cloud\Environment\CloudEnvironment
     */
    public function setSiteGroup($sitegroup);

    /**
     * Returns the application's site group, this value is usually set
     * dynamically at runtime since it is usually detected from an environment
     * variable.
     *
     * @return string
     *
     * @throws \UnexpectedValueException
     */
    public function getSiteGroup();
}
