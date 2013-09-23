<?php

namespace Acquia\Search;

use Acquia\Common\AcquiaService;
use Acquia\Network\Subscription;

class AcquiaSearchService extends AcquiaService
{
    /**
     * @var boolean
     */
    protected static $https = true;

    /**
     * {@inheritdoc}
     *
     * @return \Acquia\Search\AcquiaSearchService
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

            $config = array(
                'class' => __CLASS__,
                'services' => array(),
            );

            foreach ($subscription['heartbeat_data']['search_cores'] as $indexInfo) {
                $config['services'][$indexInfo['core_id']] = array(
                    'class' => 'Acquia\Search\Client\AcquiaSearchClient',
                    'params' => array(
                        'base_url' => self::getProtocol() . $indexInfo['balancer'],
                        'index_id' => $indexInfo['core_id'],
                        'network_key' => $subscription->getKey(),
                        'salt' => $subscription['derived_key_salt'],
                    ),
                );
            }
        }

        return parent::factory($config, $globalParameters);
    }

    /**
     * @param boolean $https
     */
    public static function useHttps($https = true)
    {
        self::$https = (bool) $https;
    }

    /**
     * @return string
     */
    public static function getProtocol()
    {
        return self::$https ? 'https://' : 'http://';
    }

    /**
     * @return array
     */
    public function getIndexes()
    {
        return array_keys($this->builderConfig);
    }
}
