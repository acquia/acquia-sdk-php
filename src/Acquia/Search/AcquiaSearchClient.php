<?php

namespace Acquia\Search;

use Acquia\Rest\ServiceManagerAware;
use Guzzle\Common\Collection;
use PSolr\Client\SolrClient;

class AcquiaSearchClient extends SolrClient implements ServiceManagerAware
{
    /**
     * {@inheritdoc}
     *
     * Sets the HMAC authentication plugin, sets the base_path to point to the
     * correct index.
     */
    public static function factory($config = array())
    {
        // We just use this for validation. The configs are set in the parent's
        // factory methid.
        Collection::fromConfig($config, array(), array('index_id', 'derived_key'));
        $solr = parent::factory($config);

        // Get the configs relevant to Acquia Search.
        $indexId = $solr->getConfig('index_id');
        $derivedKey = $solr->getConfig('derived_key');

        // Set the base bath to point to the configured index.
        $solr->getConfig()->set('base_path', '/solr/' . $indexId);

        // Attach the Acquia Search HMAC Authentication plugin to the client.
        $signature = new Signature($derivedKey);
        $plugin = new AcquiaSearchAuthPlugin($indexId, $signature);
        $solr->addSubscriber($plugin);

        return $solr;
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
