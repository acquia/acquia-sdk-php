<?php

namespace Acquia\Test\Search;

use Acquia\Common\RandomStringNoncer;
use Acquia\Search\AcquiaSearchAuthPlugin;

class AuthPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Acquia\Search\AcquiaSearchAuthPlugin
     */
    public function getAuthPlugin()
    {
        return new AcquiaSearchAuthPlugin('testid', 'testkey', new RandomStringNoncer());
    }

    public function testGetters()
    {
        $noncer = new RandomStringNoncer();
        $plugin = new AcquiaSearchAuthPlugin('testid', 'testkey', $noncer);

        $this->assertEquals('testid', $plugin->getIndexId());
        $this->assertEquals('testkey', $plugin->getDerivedKey());
        $this->assertEquals($noncer, $plugin->getNoncer());
    }

    public function testSetters()
    {
        $plugin = $this->getAuthPlugin();

        $plugin->setIndexId('anotherid');
        $this->assertEquals('anotherid', $plugin->getIndexId());

        $plugin->setRequestTime(123);
        $this->assertEquals(123, $plugin->getRequestTime());
    }
}
