<?php

namespace Acquia\Network;

use Acquia\Common\AcquiaService;

class AcquiaNetworkService extends AcquiaService
{
    /**
     * {@inheritdoc}
     *
     * @return \Acquia\Network\AcquiaNetworkService
     */
    public static function factory($config = null, array $globalParameters = array())
    {
        if ($config instanceof Subscription) {
            $subscription = $config;

            $config = array(
                'class' => __CLASS__,
                'services' => array(),
            );

            $networkId = $subscription->getId();
            $config['services'] = array(
                $networkId => array(
                    'class' => 'Acquia\Network\Client\AcquiaSearchClient',
                    'params' => array(
                        'network_id' => $networkId,
                        'network_key' => $subscription->getKey(),
                    ),
                ),
            );

        }

        return parent::factory($config, $globalParameters);
    }
}
