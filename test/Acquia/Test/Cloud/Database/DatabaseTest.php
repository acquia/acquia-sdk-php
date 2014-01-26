<?php

namespace Acquia\Test\Cloud\Database;

use Acquia\Cloud\Database\Database;
use Acquia\Common\Environment;
use Acquia\Common\Json;

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
        $environment = new Environment();
        $environment->setEnvironment(Environment::PRODUCTION);

        $database = new Database(self::SITEGROUP);
        return $database
            ->setEnvironment($environment)
            ->setResolver(new TestResolver())
            ->setCredentialsFilepath(__DIR__ . '/json/creds.json')
        ;
    }

    public function testGetSitegroup()
    {
        $database = new Database(self::SITEGROUP);
        $this->assertEquals(self::SITEGROUP, $database->getSitegroup());
    }

    public function testSetEnvironment()
    {
        $environment = new Environment();
        $database = new Database(self::SITEGROUP);
        $objectChaining = $database->setEnvironment($environment);

        $this->assertEquals($database, $objectChaining);
        $this->assertEquals($environment, $database->getEnvironment());
    }

    public function testGetDefaultEnvironment()
    {
        $database = new Database(self::SITEGROUP);
        $environment = $database->getEnvironment();
        $this->assertInstanceOf('\Acquia\Cloud\Environment\CloudEnvironment', $environment);
    }

    public function testSetResolver()
    {
        $resolver = new TestResolver();
        $database = new Database(self::SITEGROUP);
        $objectChaining = $database->setResolver($resolver);

        $this->assertEquals($database, $objectChaining);
        $this->assertEquals($resolver, $database->getResolver());
    }

    public function testGetDefaultResolver()
    {
        $database = new Database(self::SITEGROUP);
        $resolver = $database->getResolver();
        $this->assertInstanceOf('\Net_DNS2_Resolver', $resolver);
    }

    public function testSetCredentialsFilepath()
    {
        $database = new Database(self::SITEGROUP);
        $objectChaining = $database->setCredentialsFilepath('/test/path');

        $this->assertEquals($database, $objectChaining);
        $this->assertEquals('/test/path', $database->getCredentialsFilepath());
    }

    public function testGetDefaultCredentialsFilepath()
    {
        $environment = new Environment();
        $environment->setEnvironment(Environment::PRODUCTION);
        $database = new Database(self::SITEGROUP);
        $database->setEnvironment($environment);

        $expected = '/var/www/site-php/' . self::SITEGROUP . Environment::PRODUCTION . '/creds.json';
        $this->assertEquals($expected, $database->getCredentialsFilepath());
    }

    public function testParseCredentialsFile()
    {
        $database = new Database(self::SITEGROUP);

        $filepath = __DIR__ . '/json/creds.json';
        $expected = Json::decode(file_get_contents($filepath));
        $this->assertEquals($expected, $database->parseCredentialsFile($filepath));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidCredentialsFile()
    {
        $database = new Database(self::SITEGROUP);
        $database->parseCredentialsFile(__DIR__ . '/json/bad-file.json');
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
}
