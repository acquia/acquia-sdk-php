<?php

namespace Acquia\Test\Cloud\Database;

use Acquia\Cloud\Database\DatabaseCredentials;

class DatabaseCredentialsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Acquia\Cloud\Database\DatabaseCredentials
     */
    public function getDatabaseCredentials()
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

        return new DatabaseCredentials($data);
    }

    public function testFetchId()
    {
        $this->assertEquals('1234', $this->getDatabaseCredentials()->id());
    }

    public function testFetchClusterId()
    {
        $this->assertEquals('5678', $this->getDatabaseCredentials()->clusterId());
    }

    public function testFetchRole()
    {
        $this->assertEquals('mysite', $this->getDatabaseCredentials()->role());
    }

    public function testFetchDatabaseName()
    {
        $this->assertEquals('mysiteprod', $this->getDatabaseCredentials()->databaseName());
    }

    public function testFetchUsername()
    {
        $this->assertEquals('myusername', $this->getDatabaseCredentials()->username());
    }

    public function testFetchPassword()
    {
        $this->assertEquals('abcdefg', $this->getDatabaseCredentials()->password());
    }

    public function testFetchPort()
    {
        $this->assertEquals(3306, $this->getDatabaseCredentials()->port());
    }

    public function testFetchHost()
    {
        $this->assertEquals('staging-123', $this->getDatabaseCredentials()->host());
    }

    public function testFetchUrls()
    {
        $expected = array('staging-123' => 'mysqli://mysiteprod:abcdefg@staging-123:3306/mysiteprod');
        $this->assertEquals($expected, $this->getDatabaseCredentials()->urls());
    }

    public function testFetchActiveUrl()
    {
        $expected = 'mysqli://mysiteprod:abcdefg@staging-123:3306/mysiteprod';
        $this->assertEquals($expected, $this->getDatabaseCredentials()->activeUrl());
    }

    public function testFetchDsn()
    {
        $credentials = $this->getDatabaseCredentials();
        $expected = 'mysql:dbname=mysiteprod;host=staging-123;port=3306';
        $this->assertEquals($expected, $credentials->dsn());
        $this->assertEquals($expected, (string) $credentials);
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testFetchDsnMissingName()
    {
        $credentials = new DatabaseCredentials(array(
            'port' => 3306,
            'host' => 'staging-123',
        ));

        $credentials->dsn();
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testFetchDsnMissingHost()
    {
        $credentials = new DatabaseCredentials(array(
            'name' => 'mysiteprod',
            'port' => 3306,
        ));

        $credentials->dsn();
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testFetchDsnMissingPort()
    {
        $credentials = new DatabaseCredentials(array(
            'name' => 'mysiteprod',
            'host' => 'staging-123',
        ));

        $credentials->dsn();
    }
}
