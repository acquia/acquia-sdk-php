<?php

namespace Acquia\Search\Client;

use Acquia\Common\NoncerAbstract;

/**
 * Generates the HMAC hash used to sign Acquia Search requests.
 */
class Signature
{
    /**
     * @var \Acquia\Search\Client\DerivedKey
     */
    protected $derivedKey;

    /**
     * @var \Acquia\Common\NoncerAbstract
     */
    protected $noncer;

    /**
     * @param \Acquia\Search\Client\DerivedKey $derivedKey
     * @param \Acquia\Common\NoncerAbstract $noncer
     * @param int $requestTime
     */
    public function __construct(DerivedKey $derivedKey, NoncerAbstract $noncer)
    {
        $this->derivedKey = $derivedKey;
        $this->noncer = $noncer;
    }

    /**
     * @return \Acquia\Search\DerivedKey
     */
    public function getDerivedKey()
    {
        return $this->derivedKey;
    }

    /**
     * @return \Acquia\Common\NoncerAbstract
     */
    public function getNoncer()
    {
        return $this->noncer;
    }

    /**
     * Returns the last nonce that was generated.
     *
     * @return string
     */
    public function nonce()
    {
        return $this->noncer->getLastNonce();
    }

    /**
     * @param string $indexId
     * @param string $string
     * @param int $requestTime
     * @param string &$nonce
     *
     * @return string
     */
    public function generate($indexId, $string, $requestTime, &$nonce = null)
    {
        $nonce = $this->noncer->generate();
        $derivedKey = $this->derivedKey->generate($indexId);
        return hash_hmac('sha1', $requestTime . $nonce . $string, $derivedKey);
    }
}
