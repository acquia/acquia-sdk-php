<?php

namespace Acquia\Cloud\Database;

use Acquia\Common\Environment;
use Acquia\Common\Json;
use Acquia\Cloud\Environment\CloudEnvironment;

class Database
{
    /**
     * @var string
     */
    protected $sitegroup;

    /**
     * @var \Acquia\Common\Environment
     */
    private $environment;

    /**
     * @var \Net_DNS2_Resolver
     */
    private $resolver;

    /**
     * @var string
     */
    private $filepath;

    /**
     * @param string $sitegroup
     */
    public function __construct($sitegroup)
    {
        $this->sitegroup = $sitegroup;
    }

    /**
     * @return string
     */
    public function getSitegroup()
    {
        return $this->sitegroup;
    }

    /**
     * @param \Acquia\Common\Environment $environment
     *
     * @return \Acquia\Cloud\Database\Database
     */
    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * @return \Acquia\Common\Environment
     */
    public function getEnvironment()
    {
        if (!isset($this->environment)) {
            $this->environment = new CloudEnvironment();
        }
        return $this->environment;
    }

    /**
     * @param \Net_DNS2_Resolver $resolver
     *
     * @return \Acquia\Cloud\Database\Database
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
     * @return \Acquia\Cloud\Database\Database
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
            $settingsDir = $this->sitegroup . $this->getEnvironment();
            $this->filepath = '/var/www/site-php/' . $settingsDir . '/creds.json';
        }
        return $this->filepath;
    }

    /**
     * @param string $filepath
     *
     * @return array
     *
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     */
    public function parseCredentialsFile($filepath)
    {
        if (!file_exists($filepath)) {
            throw new \RuntimeException('File not found: ' . $filepath);
        }
        return Json::decode(file_get_contents($filepath));
    }

    /**
     * @param string $dbName
     *
     * @throws \OutOfBoundsException
     *
     * @return \Acquia\Cloud\Database\Credentials
     */
    public function credentials($dbName)
    {
        $filepath  = $this->getCredentialsFilepath();
        $databases = $this->parseCredentialsFile($filepath);

        if (!isset($databases['databases'][$dbName])) {
            throw new \OutOfBoundsException('Invalid database: ' . $dbName);
        }

        $database = $databases['databases'][$dbName];
        $host = $this->getCurrentHost($database['db_cluster_id']);
        $database['host'] = ($host) ?: key($database['db_url_ha']);

        return new Credentials($database);
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
