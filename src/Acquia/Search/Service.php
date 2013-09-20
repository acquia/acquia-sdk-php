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
     * @return Indexes
     */
    public function indexes()
    {
        $indexes = array();
        foreach ($this->subscription['heartbeat_data']['search_cores'] as $indexInfo) {
            $baseUrl = 'https://' . $indexInfo['balancer'];
            $indexId = $indexInfo['core_id'];
            $indexes[$indexId] = new Index($this->subscription, $baseUrl, $indexId);
        }
        return new Indexes($indexes);
    }
}
