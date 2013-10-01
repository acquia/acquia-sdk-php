<?php

namespace Acquia\Test\Cloud\Api;

use Acquia\Cloud\Api\CloudApiAuthPlugin;

class CloudApiAuthPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Acquia\Cloud\Api\CloudApiAuthPlugin
     */
    public function getAuthPlugin()
    {
        return new CloudApiAuthPlugin('test-username', 'test-password');
    }

    public function testGetters()
    {
        $plugin = $this->getAuthPlugin();
        $this->assertEquals('test-username', $plugin->getUsername());
        $this->assertEquals('test-password', $plugin->getPassword());
    }
}
