<?php

namespace Acquia\Test\Cloud\Api;

use Acquia\Cloud\Api\Response\Object;

class CloudApiObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadFromString()
    {
        $object = new Object('testvalue');
        $this->assertEquals('testvalue', (string) $object);
    }

    public function testIdColumn()
    {
        $data = array(
            'name'    => 'test1',
            'altname' => 'test2',
        );

        $object = new Object($data);
        $this->assertEquals('name', $object->getIdColumn());
        $this->assertEquals('test1', (string) $object);

        $object->setIdColumn('altname');
        $this->assertEquals('altname', $object->getIdColumn());
        $this->assertEquals('test2', (string) $object);
    }
}
