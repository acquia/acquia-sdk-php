<?php

namespace Acquia\Test\Cloud\Environment;

use Acquia\Cloud\Database\DatabaseService;
use Acquia\Cloud\Memcache\MemcacheService;
use Acquia\Cloud\Environment\LocalEnvironment;
use Acquia\Environment\Environment;

class LocalEnvironmentTest extends \PHPUnit_Framework_TestCase
{
    const SITEGROUP = 'mysite';

    public function testEnvironment()
    {
        $env = new LocalEnvironment(self::SITEGROUP);
        $this->assertEquals(Environment::LOCAL, $env->getEnvironment());
    }

    public function testIsNotAcquia()
    {
        $env = new LocalEnvironment(self::SITEGROUP);
        $this->assertFalse($env->isAcquia());
    }

    public function testGetDefaultSiteGroup()
    {
        $env = new LocalEnvironment(self::SITEGROUP);
        $this->assertEquals(self::SITEGROUP, $env->getSiteGroup());
    }

    public function testSetSiteGroup()
    {
        $env = new LocalEnvironment(self::SITEGROUP);
        $env->setSiteGroup('anothersite');
        $this->assertEquals('anothersite', $env->getSiteGroup());
    }

    public function testAddDatabaseCredentials()
    {
        $env = new LocalEnvironment(self::SITEGROUP);
        $return = $env->addDatabaseCredentials('acquianame', 'localname', 'db_uname', 'db_pass', '127.0.0.1', 33066);

        $this->assertEquals($env, $return);

        $database = new DatabaseService($env);
        $creds = $database->credentials('acquianame');

        $this->assertEquals('db_uname', $creds->username());
        $this->assertEquals('db_pass', $creds->password());
        $this->assertEquals('mysql:dbname=localname;host=127.0.0.1;port=33066', (string) $creds);
    }

    public function testAddMemcacheCredentials()
    {
        $env = new LocalEnvironment(self::SITEGROUP);
        $return = $env->addMemcacheCredentials('localhost', 12345);

        $this->assertEquals($env, $return);

        $memcache = new MemcacheService($env);
        $creds = $memcache->credentials();

        $this->assertTrue(is_array($creds));
        $this->assertEquals(1, count($creds));

        foreach ($creds as $server) {
            $this->assertInstanceOf('\Acquia\Cloud\Memcache\MemcacheCredentials', $server);
            $this->assertEquals('localhost', $server->host());
            $this->assertEquals('12345', $server->port());
            $this->assertEquals('localhost:12345', (string) $server);
        }
    }
}
