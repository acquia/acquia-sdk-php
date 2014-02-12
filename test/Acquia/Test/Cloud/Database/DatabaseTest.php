<?php

namespace Acquia\Test\Cloud\Database;

use Acquia\Cloud\Database\Database;
use Acquia\Cloud\Environment\CloudEnvironment;
use Acquia\Common\Json;
use Acquia\Common\Environment;

class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    const SITEGROUP = 'mysite';

    protected $originalSiteGroup;

    public function setUp()
    {
        $this->originalSiteGroup = getenv('AH_SITE_GROUP');
        putenv('AH_SITE_GROUP=' . self::SITEGROUP);
        parent::setUp();
    }

    public function tearDown()
    {
        if ($this->originalSiteGroup) {
            putenv('AH_SITE_GROUP=' . $this->originalSiteGroup);
        } else {
            putenv('AH_SITE_GROUP');
        }
        parent::tearDown();
    }

    /**
     * Helper function that returns a database object for the prod environment.
     *
     * @return \Acquia\Cloud\Database\Database
     */
    public function getProductionDatabase()
    {
        $environment = new CloudEnvironment();
        $environment->setEnvironment(Environment::PRODUCTION);

        $database = new Database();
        return $database
            ->setEnvironment($environment)
            ->setResolver(new TestResolver())
            ->setCredentialsFilepath(__DIR__ . '/json/creds.json')
        ;
    }

    public function testSetSitegroup()
    {
        $database = new Database();
        $database->setSiteGroup('anothergroup');
        $this->assertEquals('anothergroup', $database->getSiteGroup());
    }

    public function testGetSitegroupFromEnvironment()
    {
        $originalSitegroup = getenv('AH_SITE_GROUP');
        putenv('AH_SITE_GROUP=' . self::SITEGROUP);

        $database = new Database();
        $this->assertEquals(self::SITEGROUP, $database->getSitegroup());

        if ($originalSitegroup) {
            putenv('AH_SITE_GROUP=' . $originalSitegroup);
        } else {
            putenv('AH_SITE_GROUP');
        }
    }

    public function testSetEnvironment()
    {
        $environment = new CloudEnvironment();
        $database = new Database();
        $objectChaining = $database->setEnvironment($environment);

        $this->assertEquals($database, $objectChaining);
        $this->assertEquals($environment, $database->getEnvironment());
    }

    public function testGetDefaultEnvironment()
    {
        $database = new Database();
        $environment = $database->getEnvironment();
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

    public function testSetCredentialsFilepath()
    {
        $database = new Database();
        $objectChaining = $database->setCredentialsFilepath('/test/path');

        $this->assertEquals($database, $objectChaining);
        $this->assertEquals('/test/path', $database->getCredentialsFilepath());
    }

    public function testGetDefaultCredentialsFilepath()
    {
        $environment = new CloudEnvironment();
        $environment->setEnvironment(Environment::PRODUCTION);
        $database = new Database();
        $database->setEnvironment($environment);

        $expected = '/var/www/site-php/' . self::SITEGROUP . Environment::PRODUCTION . '/creds.json';
        $this->assertEquals($expected, $database->getCredentialsFilepath());
    }

    public function testParseCredentialsFile()
    {
        $database = new Database();

        $filepath = __DIR__ . '/json/creds.json';
        $expected = Json::decode(file_get_contents($filepath));
        $this->assertEquals($expected, $database->parseCredentialsFile($filepath));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidCredentialsFile()
    {
        $database = new Database();
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
