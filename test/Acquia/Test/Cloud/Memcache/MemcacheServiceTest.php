<?php

namespace Acquia\Test\Cloud\Memcache;

use Acquia\Cloud\Environment\CloudEnvironment;
use Acquia\Cloud\Memcache\MemcacheService;
use Acquia\Environment\Environment;

class MemcacheServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Helper function that returns a Memcache service object.
     *
     * @return \Acquia\Cloud\Memcache\MemcacheService
     */
    public function getMemcacheService()
    {
        $environment = new CloudEnvironment();
        $environment
            ->setEnvironment(Environment::PRODUCTION)
            ->setCredentialsFilepath(__DIR__ . '/../Environment/json/creds.json')
        ;

        $service = new MemcacheService();
        $service->setCloudEnvironment($environment);
        return $service;
    }

    public function testSetEnvironment()
    {
        $environment = new CloudEnvironment();
        $service = new MemcacheService();
        $objectChaining = $service->setCloudEnvironment($environment);

        $this->assertEquals($service, $objectChaining);
        $this->assertEquals($environment, $service->getCloudEnvironment());
    }

    public function testGetDefaultEnvironment()
    {
        $service = new MemcacheService();
        $environment = $service->getCloudEnvironment();
        $this->assertInstanceOf('\Acquia\Cloud\Environment\CloudEnvironment', $environment);
    }

    public function testCredentials()
    {
        $service = $this->getMemcacheService();
        $creds   = $service->credentials();

        $this->assertTrue(is_array($creds));
        $this->assertEquals(1, count($creds));

        foreach ($creds as $server) {
            $this->assertInstanceOf('\Acquia\Cloud\Memcache\MemcacheCredentials', $server);
            $this->assertEquals('ded-123.stage-one.hosting.acquia.com', $server->host());
            $this->assertEquals('11211', $server->port());
            $this->assertEquals('ded-123.stage-one.hosting.acquia.com:11211', (string) $server);
        }
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testNoServers()
    {
        $environment = new CloudEnvironment();
        $environment
            ->setEnvironment(Environment::PRODUCTION)
            ->setCredentialsFilepath(__DIR__ . '/json/no-servers.json')
        ;

        $service = new MemcacheService();
        $service->setCloudEnvironment($environment);
        $service->credentials();
    }
}
