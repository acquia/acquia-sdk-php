<?php

namespace Acquia\Search;

use Acquia\Common\AcquiaServiceManagerAware;
use PSolr\Client\SolrClient;

class AcquiaSearchClient extends SolrClient implements AcquiaServiceManagerAware
{
    /**
     * {@inheritdoc}
     */
    public static function getConfigRequired()
    {
        return parent::getConfigRequired() + array(
            'index_id',
            'derived_key',
        );
    }

    /**
     * {@inheritdoc}
     *
     * Sets the HMAC authentication plugin, sets the base_path to point to the
     * correct index.
     */
    public static function factory($config = array())
    {
        $solr = parent::factory($config);

        // Get the configs relevant to Acquia Search.
        $indexId = $solr->getConfig('index_id');
        $derivedKey = $solr->getConfig('derived_key');

        // Set the base bath to point to the configured index.
        $solr->getConfig()->set('base_path', '/solr/' . $indexId);

        // Attach the Acquia Search HMAC Authentication plugin to the client.
        $plugin = new AcquiaSearchAuthPlugin($indexId, $derivedKey);
        $solr->addSubscriber($plugin);

        return $solr;
    }

    /**
     * {@inheritdoc}
     */
    public function getBuilderClass()
    {
        return 'Acquia\Search\AcquiaSearchService';
    }

    /**
     * {@inheritdoc}
     */
    public function getBuilderParams()
    {
        return array(
            'base_url' => $this->getConfig('base_url'),
            'index_id' => $this->getConfig('index_id'),
            'derived_key' => $this->getConfig('derived_key'),
        );
    }
}
