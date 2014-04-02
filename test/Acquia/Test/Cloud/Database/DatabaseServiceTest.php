<?php

namespace Acquia\Test\Cloud\Database;

use Acquia\Cloud\Database\DatabaseService;
use Acquia\Cloud\Environment\CloudEnvironment;
use Acquia\Environment\Environment;

class DatabaseServiceTest extends \PHPUnit_Framework_TestCase
{
    const SITEGROUP = 'mysite';

    /**
     * Helper function that returns a database object for the prod environment.
     *
     * @return \Acquia\Cloud\Database\DatabaseService
     */
    public function getProductionDatabaseService()
    {
        $environment = new CloudEnvironment();
        $environment
            ->setEnvironment(Environment::PRODUCTION)
            ->setCredentialsFilepath(__DIR__ . '/../Environment/json/creds.json')
        ;

        $service = new DatabaseService();
        return $service
            ->setCloudEnvironment($environment)
            ->setResolver(new TestResolver())
        ;
    }

    public function testSetEnvironment()
    {
        $environment = new CloudEnvironment();
        $service = new DatabaseService();
        $objectChaining = $service->setCloudEnvironment($environment);

        $this->assertEquals($service, $objectChaining);
        $this->assertEquals($environment, $service->getCloudEnvironment());
    }

    public function testGetDefaultEnvironment()
    {
        $service = new DatabaseService();
        $environment = $service->getCloudEnvironment();
        $this->assertInstanceOf('\Acquia\Cloud\Environment\CloudEnvironment', $environment);
    }

    public function testSetResolver()
    {
        $resolver = new TestResolver();
        $service = new DatabaseService();
        $objectChaining = $service->setResolver($resolver);

        $this->assertEquals($service, $objectChaining);
        $this->assertEquals($resolver, $service->getResolver());
    }

    public function testGetDefaultResolver()
    {
        $service = new DatabaseService();
        $resolver = $service->getResolver();
        $this->assertInstanceOf('\Net_DNS2_Resolver', $resolver);
    }

    public function testResolverException()
    {
        $service = $this->getProductionDatabaseService();
        $service->getResolver()->throwException();

        $host = $service->getCurrentHost('1234');
        $this->assertEmpty($host);
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testCredentialsInvalidDatabase()
    {
        $service = $this->getProductionDatabaseService();
        $service->credentials('bad-db');
    }

    public function testCredentials()
    {
        $service      = $this->getProductionDatabaseService();
        $credentials  = $service->credentials(self::SITEGROUP);
        $expectedUrls = array('staging-123' => 'mysqli://mysiteprod:abcdefg@staging-123:3306/mysiteprod');
        $expectedUrl  = $expectedUrls['staging-123'];
        $expectedDsn  = 'mysql:dbname=mysiteprod;host=staging-123;port=3306';

        $this->assertInstanceOf('\Acquia\Cloud\Database\DatabaseCredentials', $credentials);
        $this->assertEquals('1234',         $credentials->id());
        $this->assertEquals('1234',         $credentials->clusterId());
        $this->assertEquals('mysite',       $credentials->role());
        $this->assertEquals('mysiteprod',   $credentials->databaseName());
        $this->assertEquals('mysiteprod',   $credentials->username());
        $this->assertEquals('abcdefg',      $credentials->password());
        $this->assertEquals(3306,           $credentials->port());
        $this->assertEquals('staging-123',  $credentials->host());
        $this->assertEquals($expectedUrl,   $credentials->activeUrl());
        $this->assertEquals($expectedUrls,  $credentials->urls());
        $this->assertEquals($expectedDsn,   $credentials->dsn());
        $this->assertEquals($expectedDsn,   (string) $credentials);
    }
}
