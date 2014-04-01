<?php

namespace Acquia\Test\Cloud\Database;

use Acquia\Cloud\Database\Database;
use Acquia\Cloud\Environment\CloudEnvironment;
use Acquia\Environment\Environment;

class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    const SITEGROUP = 'mysite';

    /**
     * Helper function that returns a database object for the prod environment.
     *
     * @return \Acquia\Cloud\Database\Database
     */
    public function getProductionDatabase()
    {
        $environment = new CloudEnvironment();
        $environment
            ->setEnvironment(Environment::PRODUCTION)
            ->setCredentialsFilepath(__DIR__ . '/../Environment/json/creds.json')
        ;

        $database = new Database();
        return $database
            ->setCloudEnvironment($environment)
            ->setResolver(new TestResolver())
        ;
    }

    public function testSetEnvironment()
    {
        $environment = new CloudEnvironment();
        $database = new Database();
        $objectChaining = $database->setCloudEnvironment($environment);

        $this->assertEquals($database, $objectChaining);
        $this->assertEquals($environment, $database->getCloudEnvironment());
    }

    public function testGetDefaultEnvironment()
    {
        $database = new Database();
        $environment = $database->getCloudEnvironment();
        $this->assertInstanceOf('\Acquia\Cloud\Environment\CloudEnvironment', $environment);
    }

    public function testSetResolver()
    {
        $resolver = new TestResolver();
        $database = new Database();
        $objectChaining = $database->setResolver($resolver);

        $this->assertEquals($database, $objectChaining);
        $this->assertEquals($resolver, $database->getResolver());
    }

    public function testGetDefaultResolver()
    {
        $database = new Database();
        $resolver = $database->getResolver();
        $this->assertInstanceOf('\Net_DNS2_Resolver', $resolver);
    }

    public function testResolverException()
    {
        $database = $this->getProductionDatabase();
        $database->getResolver()->throwException();

        $host = $database->getCurrentHost('1234');
        $this->assertEmpty($host);
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testCredentialsInvalidDatabase()
    {
        $database = $this->getProductionDatabase();
        $database->credentials('bad-db');
    }

    public function testCredentials()
    {
        $database     = $this->getProductionDatabase();
        $credentials  = $database->credentials(self::SITEGROUP);
        $expectedUrls = array('staging-123' => 'mysqli://mysiteprod:abcdefg@staging-123:3306/mysiteprod');
        $expectedUrl  = $expectedUrls['staging-123'];
        $expectedDsn  = 'mysql:dbname=mysiteprod;host=staging-123;port=3306';

        $this->assertInstanceOf('\Acquia\Cloud\Database\Credentials', $credentials);
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
