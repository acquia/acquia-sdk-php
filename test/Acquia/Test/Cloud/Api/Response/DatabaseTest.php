<?php

namespace Acquia\Test\Cloud\Api\Response;

use Acquia\Cloud\Api\Response\Database;

class DatabaseTest extends \PHPUnit_Framework_TestCase {

    protected $data_value = 'data_value';

    public function testDatabaseResponseConstructorWithArray()
    {
        $data = array('name' => $this->data_value);
        $response = new Database($data);
        $this->assertEquals($response['name'], $this->data_value);
        $this->assertEquals("{$response}", $this->data_value);
    }

    public function testDatabaseResponseConstructorWithString()
    {
        $response = new Database($this->data_value);
        $this->assertEquals($response['name'], $this->data_value);
        $this->assertEquals("{$response}", $this->data_value);
    }

}
