<?php

namespace Acquia\Test\Json;

use Acquia\Json\Json;

class JsonTest extends \PHPUnit_Framework_TestCase
{
    protected $testArray = array(
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
        'test' => array(
            '<foo>',
            "'bar'",
            '"baz"',
            '&blong&',
            "\xc3\xa9",
            '/path/to/api.json',
            "/another\\/path\\//to/api.json",
            '\Acquia\Json\Json',
        )
    );

    protected function getTestJson()
    {
        return file_get_contents(__DIR__ . '/json/test.json');
    }

    public function testJsonEncode()
    {
        $this->assertEquals($this->getTestJson(), Json::encode($this->testArray));
    }

    public function testJsonEncodeLocalPrettyPrint()
    {
        Json::useNativePrettyPrint(false);
        $this->assertEquals($this->getTestJson(), Json::encode($this->testArray));
        Json::useNativePrettyPrint(true);
    }

    public function testJsonDecode()
    {
        $this->assertEquals($this->testArray, Json::decode($this->getTestJson()));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseJsonFileMissing()
    {
        Json::parseFile(__DIR__ . '/json/missing-file.json');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseJsonFileUnreadable()
    {
        JsonReadFileError::parseFile(__DIR__ . '/json/test.json');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseJsonFileInvalid()
    {
        Json::parseFile(__DIR__ . '/json/invalid.json');
    }
}
