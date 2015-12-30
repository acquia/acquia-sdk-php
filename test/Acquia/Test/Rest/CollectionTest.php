<?php

namespace Acquia\Test\Rest;

use Acquia\Json\Json;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testNestedCollection()
    {
        $data = array(
            'collection' => array(
                array('name' => 'value1'),
            ),
        );

        $mock = new MockHandler([
          new Response(200, [], Json::encode($data)),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client([
          'handler' => $handler,
            'base_uri' => 'http://example.com',
        ]);

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

        $mock = new MockHandler([
          new Response(200, [], Json::encode($data)),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client([
          'handler' => $handler,
          'base_uri' => 'http://example.com',
        ]);

        $request = $client->get('/test');

        $collection = new DummyCollection($request);
        foreach ($collection as $element) {
            // We should never get here ...
        }
    }
}
