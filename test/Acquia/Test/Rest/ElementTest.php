<?php

namespace Acquia\Test\Rest;

use Acquia\Rest\Element;

class ElementTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadFromString()
    {
        $element = new Element('testvalue');
        $this->assertEquals('testvalue', (string) $element);
    }

    public function testIdColumn()
    {
        $data = array(
            'name'    => 'test1',
            'altname' => 'test2',
        );

        $element = new Element($data);
        $this->assertEquals('name', $element->getIdColumn());
        $this->assertEquals('test1', (string) $element);

        $element->setIdColumn('altname');
        $this->assertEquals('altname', $element->getIdColumn());
        $this->assertEquals('test2', (string) $element);
    }
}
