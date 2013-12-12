<?php

namespace Acquia\Test\Search;

use Acquia\Network\Subscription;
use Acquia\Search\AcquiaSearchService;

class AcquiaSearchServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return
     */
    public function getSubscriptionData()
    {
        return array(
            'key' => 'test-key',
            'derived_key_salt' => 'test-salt',
            'heartbeat_data' => array(
                'search_service_colony' => '',
                'search_cores' => array(
                    array(
                        'core_id' => 'index1',
                        'balancer' => 'balancer1.example.com',
                    ),
                    array(
                        'core_id' => 'index2',
                        'balancer' => 'balancer2.example.com',
                    ),
                ),
            ),
        );
    }

    /**
     * @return \Acquia\Network\Subscription
     */
    public function getSubscription()
    {
        return new Subscription($this->getSubscriptionData());
    }

    public function testLoadBySubscription()
    {
        $subscription = $this->getSubscription();
        $services = AcquiaSearchService::factory($subscription);

        $this->assertTrue(isset($services['index1']));
        $this->assertTrue(isset($services['index2']));
        $this->assertFalse(isset($services['index3']));

        $string = 'index1' . 'solr' . 'test-salt';
        $expectedKey = hash_hmac('sha1', str_pad($string, 80, $string), 'test-key');
        $this->assertEquals($expectedKey, $services['index1']->getConfig('derived_key'));

        $this->assertEquals('https://balancer1.example.com', $services['index1']->getConfig('base_url'));
        $this->assertEquals('/solr/index1', $services['index1']->getConfig('base_path'));
        $this->assertEquals('index1', $services['index1']->getConfig('index_id'));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testMissingDerivedKeySalt()
    {
        $data = $this->getSubscriptionData();
        unset($data['derived_key_salt']);
        $subscription = new Subscription($data);
        $services = AcquiaSearchService::factory($subscription);
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testRequireSearchServiceColony()
    {
        $data = $this->getSubscriptionData();
        unset($data['heartbeat_data']['search_service_colony']);
        $subscription = new Subscription($data);
        $services = AcquiaSearchService::factory($subscription);
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testRequireSearchCores()
    {
        $data = $this->getSubscriptionData();
        unset($data['heartbeat_data']['search_cores']);
        $subscription = new Subscription($data);
        $services = AcquiaSearchService::factory($subscription);
    }
}
