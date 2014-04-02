<?php

namespace Acquia\Cloud\Memcache;

use Acquia\Cloud\Environment\CloudEnvironment;
use Acquia\Cloud\Environment\CloudEnvironmentAware;
use Acquia\Cloud\Environment\CloudEnvironmentInterface;

class MemcacheService implements CloudEnvironmentAware, MemcacheServiceInterface
{
    /**
     * @var \Acquia\Cloud\Environment\CloudEnvironmentInterface
     */
    private $cloudEnvironment;

    /**
     * @param \Acquia\Cloud\Environment\CloudEnvironmentInterface $cloudEnvironment
     */
    public function __construct(CloudEnvironmentInterface $cloudEnvironment = null)
    {
        if ($cloudEnvironment === null) {
            $cloudEnvironment = new CloudEnvironment();
        }

        $this->setCloudEnvironment($cloudEnvironment);
    }

    /**
     * {@inheritDoc}
     *
     * @return \Acquia\Cloud\Memcache\MemcacheService
     */
    public function setCloudEnvironment(CloudEnvironmentInterface $cloudEnvironment)
    {
        $this->cloudEnvironment = $cloudEnvironment;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCloudEnvironment()
    {
        return $this->cloudEnvironment;
    }

    /**
     * {@inheritDoc}
     */
    public function credentials()
    {
        $creds = $this->cloudEnvironment->serviceCredentials();

        if (!isset($creds['memcached_servers'])) {
            throw new \OutOfBoundsException('Memcache credentials not found');
        }

        $servers = array();
        foreach ($creds['memcached_servers'] as $id => $url) {
            $data = parse_url($url);
            $servers[$id] = new MemcacheCredentials($data);
        }

        return $servers;
    }
}
