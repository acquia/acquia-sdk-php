<?php

namespace Acquia\Search;

use Acquia\Network\Subscription;

class Service
{
    /**
     * @var \Acquia\Network\Subscription
     */
    protected $subscription;

    /**
     * @var \ArrayObject
     */
    protected $indexes;

    /**
     * @param \Acquia\Network\Subscription $subscription
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * @return bool
     */
    public function enabled()
    {
        return !empty($this->subscription['search_service_enabled']);
    }

    /**
     * @param string|null $indexId
     *
     * @return \Acquia\Search\Client\AcquiaSearchClient
     */
    public function getClient($indexId = null)
    {
        if (null === $indexId) {
            $indexId = $this->subscription->id();
        }

        $indexes = $this->indexes();
        return $indexes[$indexId]->getClient();
    }

    /**
     * @return Indexes
     */
    public function indexes()
    {
        if (!isset($this->indexes)) {
            $indexes = array();
            foreach ($this->subscription['heartbeat_data']['search_cores'] as $indexInfo) {
                $baseUrl = 'https://' . $indexInfo['balancer'];
                $indexId = $indexInfo['core_id'];
                $indexes[$indexId] = new Index($this->subscription, $baseUrl, $indexId);
            }
            $this->indexes = new Indexes($indexes);
        }
        return $this->indexes;
    }
}
