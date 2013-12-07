<?php

namespace Acquia\Test\Common;

use Acquia\Common\Json;


class JsonTest extends \PHPUnit_Framework_TestCase
{
    protected function getTestJson() {
        $test_file = dirname(__FILE__) . "/.JsonTestArray.json";
        return file_get_contents($test_file);
    }

    protected function getTestArray() {
        $test_file = dirname(__FILE__) . "/.JsonTestArray.php";
        return eval('return ' . file_get_contents($test_file) . ';');
    }

    public function testJsonEncode()
    {
       $this->assertEquals($this->getTestJson(), Json::encode($this->getTestArray()));
    }

    public function testJsonDecode()
    {
        $this->assertEquals($this->getTestArray(), Json::decode($this->getTestJson()));
    }
}

