<?php

namespace Acquia\Search;

use Acquia\Network\Subscription;
use Guzzle\Service\Builder\ServiceBuilder;

class AcquiaSearchService extends ServiceBuilder
{
    /**
     * {@inheritdoc}
     *
     * @return \Guzzle\Service\Builder\ServiceBuilder
     */
    public static function factory($config = null, array $globalParameters = array())
    {
        if ($config instanceof Subscription) {

            $subscription = $config;
            if (!isset($subscription['derived_key_salt'])) {
                throw new \UnexpectedValueException('Derived key salt not found in subscription');
            }
            if (!isset($subscription['heartbeat_data']['search_service_colony'])) {
                throw new \UnexpectedValueException('Acquia Search hostname not found in subscription');
            }
            if (!isset($subscription['heartbeat_data']['search_cores'])) {
                throw new \UnexpectedValueException('Index data not found in subscription');
            }

            $derivedKey = new DerivedKey($subscription['derived_key_salt'], $subscription->getKey());

            $config = array('services' => array());
            foreach ($subscription['heartbeat_data']['search_cores'] as $indexInfo) {

                $config['services'][$indexInfo['core_id']] = array(
                    'class' => 'Acquia\Search\AcquiaSearchClient',
                    'params' => array(
                        'base_url' => 'https://' . $indexInfo['balancer'],
                        'index_id' => $indexInfo['core_id'],
                        'derived_key' => $derivedKey->generate($indexInfo['core_id']),
                    ),
                );
            }
        }

        return parent::factory($config, $globalParameters);
    }
}
