<?php

namespace Acquia\Search\Client;

use Guzzle\Common\Collection;
use Guzzle\Service\Client;

class AcquiaSearchClient extends Client
{
    /**
     * {@inheritdoc}
     */
    public static function factory($config = array())
    {
        $required = array(
            'base_url',
            'index_id',
            'acquia_key',
            'salt',
        );

        // Instantiate the Acquia Search plugin.
        $config = Collection::fromConfig($config, array('noncer' => null), $required);
        $client = new static($config->get('base_url'), $config);

        // Attach the Acquia Search plugin to the client.
        $client->addSubscriber(new AcquiaSearchPlugin(
            $config->get('index_id'),
            $config->get('acquia_key'),
            $config->get('salt'),
            $config->get('noncer')
        ));

        return $client;
    }
}
