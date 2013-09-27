<?php

namespace Acquia\Test\Common;

use Acquia\Common\AcquiaClient;
use Acquia\Common\RandomStringNoncer;

class AcquiaClientTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultNoncer()
    {
        $client = new AcquiaClient();
        $noncer = $client->getNoncer();
        $this->assertTrue($noncer instanceof RandomStringNoncer);
    }

    public function testSetDefaultNoncer()
    {
        AcquiaClient::setDefaultNoncerClass('Acquia\Test\Common\DummyNoncer');
        $client = new AcquiaClient();
        $noncer = $client->getNoncer();

        $this->assertEquals(AcquiaClient::getDefaultNoncerClass(), 'Acquia\Test\Common\DummyNoncer');
        $this->assertTrue($noncer instanceof DummyNoncer);
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testBadDefaultNoncer()
    {
        AcquiaClient::setDefaultNoncerClass('Acquia\Test\Common\AcquiaClientTest');
        $client = new AcquiaClient();
        $client->getNoncer();
    }
}
