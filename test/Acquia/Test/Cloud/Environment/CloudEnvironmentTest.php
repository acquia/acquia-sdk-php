<?php

namespace Acquia\Test\Cloud\Environment;

use Acquia\Cloud\Environment\CloudEnvironment;
use Acquia\Environment\Environment;
use Acquia\Json\Json;

class CloudEnvironmentTest extends \PHPUnit_Framework_TestCase
{
    const SITEGROUP = 'mysite';

    protected $originalEnv;
    protected $originalProduction;
    protected $originalSiteEnv;
    protected $originalSiteGroup;
    protected $originalServer;

    public function setUp()
    {
        $this->originalEnv = $_ENV;
        $this->originalServer = $_SERVER;
        $this->originalSiteEnv = getenv('AH_SITE_ENVIRONMENT');
        $this->originalProduction = getenv('AH_PRODUCTION');
        $this->originalSiteGroup = getenv('AH_SITE_GROUP');
        putenv('AH_SITE_GROUP=' . self::SITEGROUP);
        parent::setUp();
    }

    public function tearDown()
    {
        if ($this->originalSiteEnv) {
            putenv('AH_SITE_ENVIRONMENT=' . $this->originalSiteEnv);
            putenv('AH_PRODUCTION=' . $this->originalProduction);
            putenv('AH_SITE_GROUP=' . $this->originalSiteGroup);
        } else {
            putenv('AH_SITE_ENVIRONMENT');
            putenv('AH_PRODUCTION');
            putenv('AH_SITE_GROUP');
        }
        $_ENV = $this->originalEnv;
        $_SERVER = $this->originalServer;
        parent::tearDown();
    }

    public function testIsAcquia()
    {
        putenv('AH_SITE_ENVIRONMENT=' . Environment::PRODUCTION);
        $env = new CloudEnvironment();
        $this->assertTrue($env->isAcquia());
    }

    public function testIsNotAcquia()
    {
        putenv('AH_SITE_ENVIRONMENT=' . Environment::LOCAL);
        $env = new CloudEnvironment();
        $this->assertFalse($env->isAcquia());
    }

    public function testSetSitegroup()
    {
        $env = new CloudEnvironment();
        $env->setSiteGroup('anothergroup');
        $this->assertEquals('anothergroup', $env->getSiteGroup());
    }

    public function testGetSitegroupFromEnvironment()
    {
        putenv('AH_SITE_GROUP=' . self::SITEGROUP);
        $env = new CloudEnvironment();
        $this->assertEquals(self::SITEGROUP, $env->getSitegroup());
    }

    public function testProdEnvironment()
    {
        putenv('AH_SITE_ENVIRONMENT=prod');
        $env = new CloudEnvironment();
        $this->assertEquals((string) $env, Environment::PRODUCTION);
    }

    public function testStageEnvironment()
    {
        putenv('AH_SITE_ENVIRONMENT=test');
        $env = new CloudEnvironment();
        $this->assertEquals((string) $env, Environment::STAGING);
    }

    public function testDevEnvironment()
    {
        putenv('AH_SITE_ENVIRONMENT=dev');
        $env = new CloudEnvironment();
        $this->assertEquals((string) $env, Environment::DEVELOPMENT);
    }

    public function testLocalEnvironment()
    {
        putenv('AH_SITE_ENVIRONMENT');
        $env = new CloudEnvironment();
        $this->assertEquals((string) $env, Environment::LOCAL);
    }

    public function testIsProduction()
    {
        putenv('AH_PRODUCTION=1');
        $env = new CloudEnvironment();
        $this->assertTrue($env->isProduction());
    }

    public function testIsNonProduction()
    {
        putenv('AH_PRODUCTION=0');
        $env = new CloudEnvironment();
        $this->assertFalse($env->isProduction());
    }

    public function testSiteGroup()
    {
        putenv('AH_SITE_GROUP=mysite');
        $env = new CloudEnvironment();
        $this->assertEquals('mysite', $env->getSiteGroup());
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testNoSiteGroup()
    {
        putenv('AH_SITE_GROUP=');
        unset($_SERVER['AH_SITE_GROUP']);
        unset($_ENV['AH_SITE_GROUP']);
        $env = new CloudEnvironment();
        $env->getSiteGroup();
    }

    public function testSetCredentialsFilepath()
    {
        $env = new CloudEnvironment();
        $objectChaining = $env->setCredentialsFilepath('/test/path');

        $this->assertEquals($env, $objectChaining);
        $this->assertEquals('/test/path', $env->getCredentialsFilepath());
    }

    public function testGetDefaultCredentialsFilepath()
    {
        $env = new CloudEnvironment();
        $env->setEnvironment(Environment::PRODUCTION);

        $expected = '/var/www/site-php/' . self::SITEGROUP . '.' . Environment::PRODUCTION . '/creds.json';
        $this->assertEquals($expected, $env->getCredentialsFilepath());
    }

    public function testParseCredentialsFile()
    {
        $filepath = __DIR__ . '/json/creds.json';

        $env = new CloudEnvironment();
        $env->setCredentialsFilepath($filepath);

        $expected = Json::decode(file_get_contents($filepath));
        $this->assertEquals($expected, $env->serviceCredentials());
    }
}
