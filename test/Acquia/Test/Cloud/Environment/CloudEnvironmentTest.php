<?php

namespace Acquia\Test\Cloud\Environment;

use Acquia\Cloud\Environment\CloudEnvironment;
use Acquia\Environment\Environment;
use Acquia\Json\Json;

class CloudEnvironmentTest extends \PHPUnit_Framework_TestCase
{
    const SITEGROUP = 'mysite';
    const SITENAME = 'mysiteenvdev';
    const CURRENTREGION = 'us-east-1';

    protected $originalEnv;
    protected $originalProduction;
    protected $originalSiteEnv;
    protected $originalSiteGroup;
    protected $originalSiteName;
    protected $originalCurrentRegion;
    protected $originalServer;

    public function setUp()
    {
        $this->originalEnv = $_ENV;
        $this->originalServer = $_SERVER;
        $this->originalSiteEnv = getenv('AH_SITE_ENVIRONMENT');
        $this->originalProduction = getenv('AH_PRODUCTION');
        $this->originalSiteGroup = getenv('AH_SITE_GROUP');
        $this->originalSiteName = getenv('AH_SITE_NAME');
        $this->originalCurrentRegion = getenv('AH_CURRENT_REGION');
        putenv('AH_SITE_GROUP=' . self::SITEGROUP);
        putenv('AH_SITE_NAME=' . self::SITENAME);
        putenv('AH_CURRENT_REGION=' . self::CURRENTREGION);
        parent::setUp();
    }

    public function tearDown()
    {
        if ($this->originalSiteEnv) {
            putenv('AH_SITE_ENVIRONMENT=' . $this->originalSiteEnv);
            putenv('AH_PRODUCTION=' . $this->originalProduction);
            putenv('AH_SITE_GROUP=' . $this->originalSiteGroup);
            putenv('AH_SITE_NAME=' . $this->originalSiteName);
            putenv('AH_CURRENT_REGION=' . $this->originalCurrentRegion);
        } else {
            putenv('AH_SITE_ENVIRONMENT');
            putenv('AH_PRODUCTION');
            putenv('AH_SITE_GROUP');
            putenv('AH_SITE_NAME');
            putenv('AH_CURRENT_REGION');
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

    public function testSetSitename()
    {
        $env = new CloudEnvironment();
        $env->setSiteName('anothersite');
        $this->assertEquals('anothersite', $env->getSiteName());
    }


    public function testSetCurrentregion()
    {
        $env = new CloudEnvironment();
        $env->setCurrentRegion('us-west-2');
        $this->assertEquals('us-west-2', $env->getCurrentRegion());
    }

    public function testGetSitegroupFromEnvironment()
    {
        putenv('AH_SITE_GROUP=' . self::SITEGROUP);
        $env = new CloudEnvironment();
        $this->assertEquals(self::SITEGROUP, $env->getSitegroup());
    }

    public function testGetSitenameFromEnvironment()
    {
       putenv('AH_SITE_NAME=' . self::SITENAME);
       $env = new CloudEnvironment();
       $this->assertEquals(self::SITENAME, $env->getSitename());
    }

    public function testGetCurrentregionFromEnvironment()
    {
        putenv('AH_CURRENT_REGION=' . self::CURRENTREGION);
        $env = new CloudEnvironment();
        $this->assertEquals(self::CURRENTREGION, $env->getCurrentRegion());
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

    public function testSiteName()
    {
        putenv('AH_SITE_NAME=mysitedev');
        $env = new CloudEnvironment();
        $this->assertEquals('mysitedev', $env->getSiteName());
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testNoSiteName()
    {
        putenv('AH_SITE_NAME=');
        unset($_SERVER['AH_SITE_NAME']);
        unset($_ENV['AH_SITE_NAME']);
        $env = new CloudEnvironment();
        $env->getSiteName();
    }

    public function testCurrentRegion()
    {
        putenv('AH_CURRENT_REGION=us-east-1');
        $env = new CloudEnvironment();
        $this->assertEquals('us-east-1', $env->getCurrentRegion());
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testNoCurrentRegion()
    {
        putenv('AH_CURRENT_REGION=');
        unset($_SERVER['AH_CURRENT_REGION']);
        unset($_ENV['AH_CURRENT_REGION']);
        $env = new CloudEnvironment();
        $env->getCurrentRegion();
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
