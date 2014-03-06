<?php

namespace Acquia\Test\Network;

use Acquia\Network\Signature;

class SignatureTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
         parent::setUp();
         Signature::setDefaultNoncerClass('Acquia\Test\Network\MockNoncer');
    }

    public function tearDown()
    {
         parent::tearDown();
         Signature::setDefaultNoncerClass('Acquia\Rest\RandomStringNoncer');
    }

    /**
     * @return \Acquia\Network\Signature
     */
    public function getSignature()
    {
        $signature = new Signature('secretkey');
        $signature->setRequestTime('1390764701');
        return $signature;
    }

    public function testGenerateHashV1()
    {
        $signature = $this->getSignature();
        $params = array(
            'somekey'     => 'somevalue',
            'rpc_version' => 1,
        );
        $this->assertEquals('k2vo2SWXKsW6Z2K2Cc/o2m29HeE=', $signature->generate($params));
    }

    public function testGenerateHashV2()
    {
        $signature = $this->getSignature();
        $params = array(
            'somekey'     => 'somevalue',
            'rpc_version' => 2,
        );
        $this->assertEquals('2f8e8316810381bcf34d61c5ac13475dff8b8471', $signature->generate($params));
    }

    public function testGenerateHashV3()
    {
        $signature = $this->getSignature();
        $params = array(
            'somekey'     => 'somevalue',
            'rpc_version' => 3,
        );
        $this->assertEquals('7ec383d0bd91197944ac39d7c34974889cb03dec', $signature->generate($params));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testInvalidParams()
    {
        $signature = $this->getSignature();
        $signature->generate('invalid-value');
    }
}
