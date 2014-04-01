<?php

namespace Acquia\Cloud\Environment;

interface CloudEnvironmentAware
{
    /**
     * Sets the Acquia Cloud environment.
     *
     * @param \Acquia\Cloud\Environment\CloudEnvironmentInterface $cloudEnvironment
     *
     * @return \Acquia\Cloud\Environment\CloudEnvironmentAware
     */
    public function setCloudEnvironment(CloudEnvironmentInterface $cloudEnvironment);

    /**
     * Sets the Acquia Cloud environment.
     *
     * @return \Acquia\Cloud\Environment\CloudEnvironmentInterface
     */
    public function getCloudEnvironment();
}
