<?php

namespace Acquia\Cloud\Environment;

use Acquia\Environment\Environment;

/**
 * @see https://docs.acquia.com/cloud/configure/env-variable
 */
class LocalEnvironment extends Environment implements CloudEnvironmentInterface
{
    /**
     * @var array
     */
    protected $creds = array();

    /**
     * @var string
     */
    protected $sitegroup;

    /**
     * @param string $sitegroup
     */
    public function __construct($sitegroup)
    {
        $this->sitegroup = $sitegroup;
        parent::__construct();
    }

    /**
     * Calculates and returns the environment.
     *
     * @return string
     */
    protected function init()
    {
        return self::LOCAL;
    }

    /**
     * {@inheritDoc}
     */
    public function isAcquia()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function setSiteGroup($sitegroup)
    {
        $this->sitegroup = $sitegroup;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSiteGroup()
    {
        return $this->sitegroup;
    }

    /**
     * {@inheritDoc}
     */
    public function serviceCredentials()
    {
        return $this->creds;
    }

    /**
     * Adds credentials to a database server.
     *
     * @param string $acquiaDbName
     * @param string $localDbName
     * @param string $username
     * @param string|null $password
     * @param string $host
     * @param integer $port
     *
     * @return \Acquia\Cloud\Environment\LocalEnvironment
     */
    public function addDatabaseCredentials($acquiaDbName, $localDbName, $username, $password = null, $host = 'localhost', $port = 3306)
    {
        $connString = $username;
        if ($password !== null) {
            $connString . ':' . $password;
        }

        $this->creds['databases'][$acquiaDbName] = array(
            'id'  => '1',
            'role' => $this->sitegroup, // Is this right? For local doesn't really matter.
            'name' => $localDbName,
            'user' => $username,
            'pass' => $password,
            'db_url_ha' => array(
                $host => "mysqli://$connString@$host:$port/mysiteprod"
            ),
            'db_cluster_id' => '1',
            'port' => $port,
        );

        return $this;
    }

    /**
     * Adds credentials to a Memcached server.
     *
     * @param string $host
     * @param string $port
     *
     * @return \Acquia\Cloud\Environment\LocalEnvironment
     */
    public function addMemcacheCredentials($host, $port)
    {
        $this->creds['memcached_servers'][] = $host . ':' . $port;
        return $this;
    }
}
