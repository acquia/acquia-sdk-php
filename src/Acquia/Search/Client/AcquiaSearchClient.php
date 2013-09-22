<?php

namespace Acquia\Search\Client;

use Guzzle\Common\Collection;
use Guzzle\Http\Url;
use Guzzle\Service\Client;

class AcquiaSearchClient extends Client
{
    /**
     * @var int
     */
    protected $maxQueryLength = 2;

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

        // Set template that doesn't expand URI template expressions.
        $client->setUriTemplate(new SolrUriTemplate());

        return $client;
    }

    /**
     * If the query string exceeds this length, a POST request is issues instead
     * of a GET request to prevent server-side errors.
     *
     * @param int $length
     *
     * @return \Acquia\Search\Client\AcquiaSearchClient
     */
    public function setMaxQueryLength($length)
    {
        $this->maxQueryLength = $length;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxQueryLength()
    {
        return $this->maxQueryLength;
    }

    /**
     * @param string $uri
     * @param array $params
     *
     * @return boolean
     */
    public function useGetMethod($uri, array $params)
    {
        $url = Url::factory($this->getBaseUrl())->combine($this->expandTemplate($uri, $params));
        return strlen($url) <= $this->maxQueryLength;
    }

    /**
     * @return string
     */
    public function basePath()
    {
        return $path = '/solr/' . $this->getConfig('index_id');
    }

    /**
     * @param array $params
     * @param array|null $headers
     * @param array $options
     *
     * @return array
     */
    public function select($params = array(), $headers = null, array $options = array())
    {
        if (is_string($params)) {
            $params = array('q' => $params);
        }

        $params['wt'] = 'json';
        $params['json.nl'] = 'json';

        $params += array(
            'defType' => 'edismax',
            'q' => '*:*',
            'rows' => 10,
            'start' => 0,
        );

        // Issue GET or POST request depending on url length.
        $uri = $this->basePath() . '/select';
        if ($this->useGetMethod($uri, $params)) {
            $options['query'] = $params;
            return $this->get($uri, $headers, $options)->send()->json();
        } else {
            unset($options['query']);
            return $this->post($uri, $headers, $params, $options)->send()->json();
        }
    }

    /**
     * @param array $params
     * @param array|null $headers
     * @param array $options
     *
     * @return array
     */
    public function ping(array $params = array(), $headers = null, array $options = array())
    {
        $params += array('wt' => 'json');
        $options['query'] = $params;
        return $this
            ->head($this->basePath() . '/admin/ping', $headers, $options)
            ->send()
            ->json()
        ;
    }
}
