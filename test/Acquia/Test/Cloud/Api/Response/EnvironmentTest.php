<?php

namespace Acquia\Test\Cloud\Api\Response;

use Acquia\Cloud\Api\Response\Environment;

class EnvironmentTest extends \PHPUnit_Framework_TestCase {

    protected $data_value = 'data_value';

    public function testEnvironmentResponseConstructorWithArray()
    {
        $data = array('name' => $this->data_value);
        $response = new Environment($data);
        $this->assertEquals($response['name'], $this->data_value);
        $this->assertEquals("{$response}", $this->data_value);
    }

    public function testEnvironmentResponseConstructorWithString()
    {
        $response = new Environment($this->data_value);
        $this->assertEquals($response['name'], $this->data_value);
        $this->assertEquals("{$response}", $this->data_value);
    }

}