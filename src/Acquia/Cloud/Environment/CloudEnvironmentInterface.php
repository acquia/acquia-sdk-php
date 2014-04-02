<?php

namespace Acquia\Cloud\Environment;

interface CloudEnvironmentInterface
{
    /**
     * Returns whether the application is running on Acquia Cloud.
     *
     * @return bool
     */
    public function isAcquia();

    /**
     * Returns an associative array
     * @return array
     *
     * @throws \RuntimeException
     */
    public function serviceCredentials();

    /**
     * Sets the application's site group.
     *
     * @param string $sitegroup
     *
     * @return \Acquia\Cloud\Environment\CloudEnvironment
     */
    public function setSiteGroup($sitegroup);

    /**
     * Returns the application's site group.
     *
     * @return string
     *
     * @throws \UnexpectedValueException
     */
    public function getSiteGroup();
}
