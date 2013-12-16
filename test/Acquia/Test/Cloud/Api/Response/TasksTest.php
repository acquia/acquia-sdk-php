<?php

namespace Acquia\Test\Cloud\Api\Response;

use Acquia\Cloud\Api\Response\Tasks;

class TasksTest extends \PHPUnit_Framework_TestCase {

    protected $data_value;

    public function __construct() {
        $this->data_value = array(
            array('id' => '1'),
            array('id' => '2'),
            array('id' => '3'),
        );
    }

    public function testTasksResponseConstructor()
    {
        $responses = new Tasks($this->data_value);
        $iterator = $responses->getIterator();
        while($iterator->valid()) {
            $response = $iterator->current();
            $this->assertEquals($response['id'], $iterator->key());
            $this->assertEquals("{$response}", $iterator->key());
            $iterator->next();
        }
    }

}
