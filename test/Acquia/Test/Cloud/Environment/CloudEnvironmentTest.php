<?php

namespace Acquia\Test\Cloud\Environment;

use Acquia\Cloud\Environment\CloudEnvironment;
use Acquia\Environment\Environment;

class CloudEnvironmentTest extends \PHPUnit_Framework_TestCase
{
    protected $originalEnv;
    protected $originalProduction;
    protected $originalSiteGroup;

    public function setUp()
    {
        $this->originalEnv = getenv('AH_SITE_ENVIRONMENT');
        $this->originalProduction = getenv('AH_PRODUCTION');
        $this->originalSiteGroup = getenv('AH_SITE_GROUP');
        parent::setUp();
    }

    public function tearDown()
    {
        if ($this->originalEnv) {
            putenv('AH_SITE_ENVIRONMENT=' . $this->originalEnv);
            putenv('AH_PRODUCTION=' . $this->originalProduction);
            putenv('AH_SITE_GROUP=' . $this->originalSiteGroup);
        } else {
            putenv('AH_SITE_ENVIRONMENT');
            putenv('AH_PRODUCTION');
            putenv('AH_SITE_GROUP');
        }
        parent::tearDown();
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
        putenv('AH_SITE_GROUP');
        $env = new CloudEnvironment();
        $env->getSiteGroup();
    }
}
