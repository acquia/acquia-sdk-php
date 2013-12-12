<?php

namespace Acquia\Test\Search;

use Acquia\Search\Signature;

class SignatureTest extends \PHPUnit_Framework_TestCase
{
    public function testSignatureGeneration()
    {
        $signature = new Signature('test-key');
        $actual = $signature->generate('test-string');

        $data = $signature->getRequestTime() . $signature->getNonce() . 'test-string';
        $expected = hash_hmac('sha1', $data, 'test-key');

        $this->assertEquals($expected, $actual);
    }
}