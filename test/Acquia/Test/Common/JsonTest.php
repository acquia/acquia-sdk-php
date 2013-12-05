<?php

namespace Acquia\Test\Common;

use Acquia\Common\Json;

class JsonTest extends \PHPUnit_Framework_TestCase
{
    protected function getTestJson() {
        return '{"foo":{"bar":"bar foo","Food":{"bar":"X","Fool":{"Foolery":{"ID":"LAMA","SortOf":"LAMA","Foot":"Lorem Aliquam Morbi Aenean","Aenean":"LAMA","Aliquam":"ABC 1234:5678","Foodie":{"para":"Lorem ipsum dolor sit amet, consectetur adipiscing elit.","FoosBall":["ABC","123"]},"Footsie":"quisquam"}}}}}';
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
            )
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

