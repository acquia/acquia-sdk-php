<?php

namespace Acquia\Search\Client;

/**
 * Generates a the derived key, which is the shared secret used to generate the
 * HMAC hash used to sign Acquia Search requests.
 */
class DerivedKey
{
    /**
     * @var string
     */
    protected $salt;

    /**
     * @var string
     */
    protected $acquiaKey;

    /**
     * @param string $salt
     *   The derived key salt, or the shared secret used to generate the key.
     * @param string $acquiaKey
     *   The Acquia Network key of the subscription the index is associated
     *   with.
     */
    public function __construct($salt, $acquiaKey)
    {
        $this->salt = $salt;
        $this->acquiaKey = $acquiaKey;
    }

    /**
     * @return string
     */
    public function getAcquiaKey()
    {
        return $this->acquiaKey;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $indexId
     *   The unique identifier of the index, e.g. ABCD-12345.
     *
     * @return string
     */
    public function generate($indexId)
    {
        $string = $indexId . 'solr' . $this->salt;
        return hash_hmac('sha1', str_pad($string, 80, $string), $this->acquiaKey);
    }
}
