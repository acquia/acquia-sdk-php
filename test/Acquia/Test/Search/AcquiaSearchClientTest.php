<?php

namespace Acquia\Test\Search;

use Acquia\Search\AcquiaSearchClient;
use Acquia\Search\AcquiaSearchAuthPlugin;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;

class AcquiaSearchClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Helper function that returns the event listener.
     *
     * @param Acquia\Search\AcquiaSearchClient $solr
     *
     * @return \Acquia\Search\AcquiaSearchAuthPlugin
     *
     * @throws \UnexpectedValueException
     */
    public function getRegisteredAuthPlugin(AcquiaSearchClient $solr)
    {
        $listeners = $solr->getEventDispatcher()->getListeners('request.before_send');
        foreach ($listeners as $listener) {
            if (isset($listener[0]) && $listener[0] instanceof AcquiaSearchAuthPlugin) {
                return $listener[0];
            }
        }

        throw new \UnexpectedValueException('Expecting subscriber Acquia\Search\AcquiaSearchAuthPlugin to be registered');
    }

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
        $hasPlugin = (boolean) $this->getRegisteredAuthPlugin($solr);
        return $this->assertTrue($hasPlugin);
    }

    public function testMockSearch()
    {
        $solr = $this->getAcquiaSearchClient();

        $mock = new MockPlugin();
        $mock->addResponse(new Response(200));
        $solr->addSubscriber($mock);

        $request = $solr->get('select');
        $request->send();

        $headers = $request->getHeaders()->get('cookie')->toArray();
        $string = join(';', $headers);

        $this->assertRegExp('/acquia_solr_time=\d+;/', $string);
        $this->assertRegExp('/acquia_solr_nonce=[a-zA-Z0-9+\/]+;/', $string);
        $this->assertRegExp('/acquia_solr_hmac=[a-f0-9]+;/', $string);
    }
}
