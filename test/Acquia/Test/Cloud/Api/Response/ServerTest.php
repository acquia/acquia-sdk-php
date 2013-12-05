<?php

namespace Acquia\Test\Cloud\Api\Response;

use Acquia\Cloud\Api\Response\Server;

class ServerTest extends \PHPUnit_Framework_TestCase {

    protected $data_value = 'data_value';

    public function testServerResponseConstructorWithArray()
    {
        $data = array('name' => $this->data_value);
        $response = new Server($data);
        $this->assertEquals($response['name'], $this->data_value);
        $this->assertEquals("{$response}", $this->data_value);
    }

    public function testServerResponseConstructorWithString()
    {
        $response = new Server($this->data_value);
        $this->assertEquals($response['name'], $this->data_value);
        $this->assertEquals("{$response}", $this->data_value);
    }

}