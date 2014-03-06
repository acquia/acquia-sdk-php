<?php

namespace Acquia\Test\Rest;

use Acquia\Rest\RandomStringNoncer;

class NoncerTest extends \PHPUnit_Framework_TestCase
{
    public function testLastNonce()
    {
        $noncer = new RandomStringNoncer();

        $nonce = $noncer->generate();
        $this->assertEquals($nonce, $noncer->getLastNonce());

        $noncer->generate();
        $this->assertNotEquals($nonce, $noncer->getLastNonce());
    }

    public function testLength()
    {
        $noncer = new RandomStringNoncer(123);
        $this->assertEquals(123, $noncer->getLength());

        $noncer->setLength(456);
        $this->assertEquals(456, $noncer->getLength());
    }

    public function testToString()
    {
        $noncer = new RandomStringNoncer();
        $nonce = (string) $noncer;
        $this->assertTrue(is_string($nonce));
        $this->assertEquals($nonce, $noncer->getLastNonce());
    }
}