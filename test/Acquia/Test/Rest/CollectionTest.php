<?php

namespace Acquia\Test\Rest;

use Acquia\Json\Json;
use Guzzle\Http\Client;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testNestedCollection()
    {
        $data = array(
            'collection' => array(
                array('name' => 'value1'),
            ),
        );

        $response = new \Guzzle\Http\Message\Response(200);
        $response->setBody(Json::encode($data));

        $mock = new \Guzzle\Plugin\Mock\MockPlugin();
        $mock->addResponse($response);

        $client = new Client('http://example.com');
        $client->addSubscriber($mock);
        $request = $client->get('/test');

        $collection = new DummyCollection($request);
        foreach ($collection as $element) {
            $this->assertInstanceOf('\Acquia\Rest\Element', $element);
            $this->assertEquals('value1', (string) $element);
        }
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testNestedCollectionNotFound()
    {
        $data = array(
            'invalidProperty' => array(
                array('name' => 'value1'),
            ),
        );

        $response = new \Guzzle\Http\Message\Response(200);
        $response->setBody(Json::encode($data));

        $mock = new \Guzzle\Plugin\Mock\MockPlugin();
        $mock->addResponse($response);

        $client = new Client('http://example.com');
        $client->addSubscriber($mock);
        $request = $client->get('/test');

        $collection = new DummyCollection($request);
        foreach ($collection as $element) {
            // We should never get here ...
        }
    }
}
