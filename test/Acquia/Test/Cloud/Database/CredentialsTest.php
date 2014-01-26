<?php

namespace Acquia\Test\Cloud\Database;

use Acquia\Cloud\Database\Credentials;

class CredentialsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Acquia\Cloud\Database\Credentials
     */
    public function getCredentials()
    {
        $data = array(
            'id' => '1234',
            'role' => 'mysite',
            'name' => 'mysiteprod',
            'user' => 'myusername',
            'pass' => 'abcdefg',
            'db_url_ha' => array(
                'staging-123' => 'mysqli://mysiteprod:abcdefg@staging-123:3306/mysiteprod',
            ),
            'db_cluster_id' => '5678', // This is different than the ID for testing purposed only.
            'port' => 3306,
            'host' => 'staging-123',
        );

        return new Credentials($data);
    }

    public function testFetchId()
    {
        $this->assertEquals('1234', $this->getCredentials()->id());
    }

    public function testFetchClusterId()
    {
        $this->assertEquals('5678', $this->getCredentials()->clusterId());
    }

    public function testFetchRole()
    {
        $this->assertEquals('mysite', $this->getCredentials()->role());
    }

    public function testFetchDatabaseName()
    {
        $this->assertEquals('mysiteprod', $this->getCredentials()->databaseName());
    }

    public function testFetchUsername()
    {
        $this->assertEquals('myusername', $this->getCredentials()->username());
    }

    public function testFetchPassword()
    {
        $this->assertEquals('abcdefg', $this->getCredentials()->password());
    }

    public function testFetchPort()
    {
        $this->assertEquals(3306, $this->getCredentials()->port());
    }

    public function testFetchHost()
    {
        $this->assertEquals('staging-123', $this->getCredentials()->host());
    }

    public function testFetchUrls()
    {
        $expected = array('staging-123' => 'mysqli://mysiteprod:abcdefg@staging-123:3306/mysiteprod');
        $this->assertEquals($expected, $this->getCredentials()->urls());
    }

    public function testFetchActiveUrl()
    {
        $expected = 'mysqli://mysiteprod:abcdefg@staging-123:3306/mysiteprod';
        $this->assertEquals($expected, $this->getCredentials()->activeUrl());
    }

    public function testFetchDsn()
    {
        $credentials = $this->getCredentials();
        $expected = 'mysql:dbname=mysiteprod;host=staging-123;port=3306';
        $this->assertEquals($expected, $credentials->dsn());
        $this->assertEquals($expected, (string) $credentials);
    }
}
