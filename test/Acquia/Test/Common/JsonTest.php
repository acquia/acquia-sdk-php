<?php

namespace Acquia\Test\Common;

use Acquia\Common\Json;


class JsonTest extends \PHPUnit_Framework_TestCase
{
    protected function getTestJson() {
        $test_file = dirname(__FILE__) . "/.test.json";
        return file_get_contents($test_file);
    }

    protected function getTestArray() {
        return array(
            "foo" => array(
                "bar" => "bar foo",
                "Food" => array(
                    "bar" => "X",
                    "Fool" => array(
                        "Foolery" => array(
                            "ID" => "LAMA",
                            "SortOf" => "LAMA",
                            "Foot" => "Lorem Aliquam Morbi Aenean",
                            "Aenean" => "LAMA",
                            "Aliquam" => "ABC 1234:5678",
                            "Foodie" => array(
                                "para" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                                "FoosBall" => array(
                                    "ABC",
                                    "123"
                                ),
                            ),
                            "Footsie" => "quisquam"
                        )
                    )
                )
            ),
            'test' => array('<foo>',"'bar'",'"baz"','&blong&', "\xc3\xa9")
        );
    }

    public function testJsonEncode()
    {
       $this->assertEquals(Json::encode($this->getTestArray()),$this->getTestJson());
    }

    public function testJsonDecode()
    {
        $this->assertEquals(Json::decode($this->getTestJson()),$this->getTestArray());
    }
}

