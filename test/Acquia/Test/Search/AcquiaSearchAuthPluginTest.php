<?php

namespace Acquia\Test\Search;

use Acquia\Search\AcquiaSearchAuthPlugin;

class AcquiaSearchAuthPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Acquia\Search\AcquiaSearchAuthPlugin
     */
    public function getAuthPlugin()
    {
        return new AcquiaSearchAuthPlugin('testid', 'testkey');
    }

    public function testGetters()
    {
        $plugin = $this->getAuthPlugin();
        $this->assertEquals('testid', $plugin->getIndexId());
        $this->assertEquals('testkey', $plugin->getDerivedKey());
    }

    public function testSetters()
    {
        $plugin = $this->getAuthPlugin();
        $plugin->setIndexId('anotherid');
        $this->assertEquals('anotherid', $plugin->getIndexId());
    }
}
