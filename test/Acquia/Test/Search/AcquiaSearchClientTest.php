<?php

namespace Acquia\Test\Search;

use Acquia\Search\AcquiaSearchClient;
use Acquia\Search\AcquiaSearchAuthPlugin;

class AcquiaSearchClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Acquia\Search\AcquiaSearchClient
     */
    public function getAcquiaSearchClient()
    {
        return AcquiaSearchClient::factory(array(
            'base_url' => 'https://search.example.com',
            'index_id' => 'test_id',
            'derived_key' => 'test_key',
        ));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRequireIndexId()
    {
        AcquiaSearchClient::factory(array(
            'derived_key' => 'test_key',
        ));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRequireDerivedKey()
    {
        AcquiaSearchClient::factory(array(
            'index_id' => 'test_id',
        ));
    }

    public function testGetBuilderClass()
    {
        $solr = $this->getAcquiaSearchClient();
        $this->assertEquals('Acquia\Search\AcquiaSearchService', $solr->getBuilderClass());
    }

    public function testGetBuilderParams()
    {
        $expected = array (
            'base_url' => 'https://search.example.com',
            'index_id' => 'test_id',
            'derived_key' => 'test_key',
        );

        $solr = $this->getAcquiaSearchClient();
        $this->assertEquals($expected, $solr->getBuilderParams());
    }

    public function testGetBasePath()
    {
        $solr = $this->getAcquiaSearchClient();
        $this->assertEquals('/solr/test_id', $solr->getConfig('base_path'));
    }

    public function testHasAuthPlugin()
    {
        $solr = $this->getAcquiaSearchClient();
        $listeners = $solr->getEventDispatcher()->getListeners('request.before_send');

        $hasPlugin = false;
        foreach ($listeners as $listener) {
            if (isset($listener[0]) && $listener[0] instanceof AcquiaSearchAuthPlugin) {
                $hasPlugin = true;
            }
        }

        return $this->assertTrue($hasPlugin);
    }
}
