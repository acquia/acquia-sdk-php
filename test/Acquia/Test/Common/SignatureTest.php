<?php

namespace Acquia\Test\Common;

use Acquia\Common\SignatureAbstract;

class SignatureTest extends \PHPUnit_Framework_TestCase
{
    public function testSecretKey()
    {
        $signature = new DummySignature('secret-key');
        $this->assertEquals('secret-key', $signature->getSecretKey());
    }

    public function testSetDefaultNoncer()
    {
        $class = 'Acquia\Test\Common\DummyNoncer';
        SignatureAbstract::setDefaultNoncerClass($class);

        $this->assertEquals($class, SignatureAbstract::getDefaultNoncerClass());

        $signature = new DummySignature('secret-key');
        $noncer = $signature->getNoncer();
        $this->assertTrue($noncer instanceof DummyNoncer);
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testSetBadDefaultNoncer()
    {
        $class = 'Acquia\Test\Common\DummyNoncer';
        SignatureAbstract::setDefaultNoncerClass('Acquia\Test\Common\DummySignature');
        $signature = new DummySignature('secret-key');

        try {
            $noncer = $signature->getNoncer();
        } catch (\Exception $e) {
            // Revert back to the original so subsequent tests don't fail.
            SignatureAbstract::setDefaultNoncerClass('Acquia\Common\RandomStringNoncer');
            throw $e;
        }
    }

    public function testNonce()
    {
        $signature = new DummySignature('secret-key');

        $nonce = $signature->generateNonce();
        $this->assertEquals($nonce, $signature->getNonce());

        $signature->generateNonce();
        $this->assertNotEquals($nonce, $signature->getNonce());
    }

    public function testRequestTime()
    {
        $signature = new DummySignature('secret-key');

        $requestTime = $signature->getRequestTime();
        sleep(1);
        $this->assertEquals($requestTime, $signature->getRequestTime());

        $signature->unsetRequestTime();
        sleep(1);
        $this->assertNotEquals($requestTime, $signature->getRequestTime());
    }

    public function testSetRequestTime()
    {
        $signature = new DummySignature('secret-key');
        $signature->setRequestTime(123);
        $this->assertEquals(123, $signature->getRequestTime());
    }
}
