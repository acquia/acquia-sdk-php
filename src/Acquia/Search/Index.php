<?php

namespace Acquia\Search;

use Acquia\Network\Subscription;

class Index
{
    /**
     * @var \Acquia\Network\Subscription
     */
    protected $subscription;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $indexId;

    /**
     * @param \Acquia\Network\Subscription $subscription
     * @param string $baseUrl
     * @param string $indexId
     */
    public function __construct(Subscription $subscription, $baseUrl, $indexId)
    {
        $this->subscription = $subscription;
        $this->baseUrl = $baseUrl;
        $this->indexId = $indexId;
    }

    /**
     * @return \Acquia\Network\Subscription $subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getIndexId()
    {
        return $this->indexId;
    }

    /**
     * @return \Acquia\Search\Client\AcquiaSearchClient
     */
    public function getClient()
    {
        return Client\AcquiaSearchClient::factory(array(
            'base_url' => $this->baseUrl,
            'index_id' => $this->indexId,
            'acquia_key' => $this->subscription->key(),
            'salt' => $this->subscription['derived_key_salt'],
        ));
    }
}
