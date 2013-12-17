<?php

namespace Acquia\Test\Cloud\Api\Response;

use Acquia\Cloud\Api\Response\Task;

class TaskTest extends \PHPUnit_Framework_TestCase {

    protected $data_value = array('id' => 1);

    public function testTaskResponseConstructorWithArray()
    {
        $response = new Task($this->data_value);
        $this->assertEquals($response['id'], $this->data_value['id']);
        $this->assertEquals("{$response}", $this->data_value['id']);
    }

    public function testTaskResponseConstructorWithString()
    {
        $this->data_value = "1";
        $response = new Task($this->data_value);
        $this->assertEquals($response['id'], $this->data_value);
        $this->assertEquals("{$response}", $this->data_value);
    }

}