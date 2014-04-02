<?php

namespace Acquia\Cloud\Database;

use Acquia\Cloud\Environment\CloudEnvironment;
use Acquia\Cloud\Environment\CloudEnvironmentAware;
use Acquia\Cloud\Environment\CloudEnvironmentInterface;

class DatabaseService implements CloudEnvironmentAware, DatabaseServiceInterface
{
    /**
     * @var \Acquia\Cloud\Environment\CloudEnvironmentInterface
     */
    private $cloudEnvironment;

    /**
     * @var \Net_DNS2_Resolver
     */
    private $resolver;

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
     * @return \Acquia\Cloud\Database\DatabaseService
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
     * @param \Net_DNS2_Resolver $resolver
     *
     * @return \Acquia\Cloud\Database\DatabaseService
     */
    public function setResolver(\Net_DNS2_Resolver $resolver)
    {
        $this->resolver = $resolver;
        return $this;
    }

    /**
     * @return \Net_DNS2_Resolver
     */
    public function getResolver()
    {
        if (!isset($this->resolver)) {
            $options = array('nameservers' => array('127.0.0.1', 'dns-master'));
            $this->resolver = new \Net_DNS2_Resolver($options);
        }
        return $this->resolver;
    }

    /**
     * {@inheritDoc}
     */
    public function credentials($dbName)
    {
        $creds = $this->cloudEnvironment->serviceCredentials();

        if (!isset($creds['databases'][$dbName])) {
            throw new \OutOfBoundsException('Invalid database: ' . $dbName);
        }

        $database = $creds['databases'][$dbName];
        $host = $this->getCurrentHost($database['db_cluster_id']);
        $database['host'] = ($host) ?: key($database['db_url_ha']);

        return new DatabaseCredentials($database);
    }

    /**
     * @param in $clusterId
     *
     * @return string
     */
    public function getCurrentHost($clusterId)
    {
        try {
            $resolver = $this->getResolver();
            $response = $resolver->query('cluster-' . $clusterId . '.mysql', 'CNAME');
            return $response->answer[0]->cname;
        } catch (\Net_DNS2_Exception $e) {
            return '';
        }
    }
}
