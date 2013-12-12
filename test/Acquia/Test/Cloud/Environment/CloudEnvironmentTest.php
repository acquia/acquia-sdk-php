<?php

namespace Acquia\Test\Cloud\Environment;

use Acquia\Cloud\Environment\CloudEnvironment;
use Acquia\Common\Environment;

class CloudEnvironmentTest extends \PHPUnit_Framework_TestCase
{
    protected $originalEnv;

    public function setUp()
    {
        $this->originalEnv = getenv('AH_SITE_ENVIRONMENT');
        parent::setUp();
    }

    public function tearDown()
    {
        if ($this->originalEnv) {
            putenv('AH_SITE_ENVIRONMENT=' . $this->originalEnv);
        } else {
            putenv('AH_SITE_ENVIRONMENT');
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
}
