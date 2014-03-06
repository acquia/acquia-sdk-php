<?php

namespace Acquia\Test\Environment;

use Acquia\Environment\Environment;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultEnvironment()
    {
        $env = new Environment();
        $this->assertEquals(Environment::DEVELOPMENT, $env->getEnvironment());
    }

    public function testInitHook()
    {
        $env = new DummyEnvironment();
        $this->assertEquals('phpunit', $env->getEnvironment());
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testBadEnvironment()
    {
        $env = new Environment();
        $env->setEnvironment(false);
    }

    public function testIsProduction()
    {
        $env = new Environment();
        $this->assertFalse($env->isProduction());

        $env->setEnvironment(Environment::PRODUCTION);
        $this->assertTrue($env->isProduction());
    }

    public function testToString()
    {
        $env = new Environment();
        $this->assertEquals(Environment::DEVELOPMENT, (string) $env);
    }
}
