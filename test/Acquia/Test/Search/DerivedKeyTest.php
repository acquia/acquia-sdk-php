<?php

namespace Acquia\Test\Search;

use Acquia\Search\DerivedKey;

class DerivedKeyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $derivedKey = new DerivedKey('test-salt', 'test-key');
        $this->assertEquals('test-salt', $derivedKey->getSalt());
        $this->assertEquals('test-key', $derivedKey->getNetworkKey());
    }

    public function testDerivedKeyGeneration()
    {
        $derivedKey = new DerivedKey('test-salt', 'test-key');

        $string = 'test-id' . 'solr' . 'test-salt';
        $expected = hash_hmac('sha1', str_pad($string, 80, $string), 'test-key');

        $this->assertEquals($expected, $derivedKey->generate('test-id'));
    }
}