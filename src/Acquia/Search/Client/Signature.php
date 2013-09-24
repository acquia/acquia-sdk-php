<?php

namespace Acquia\Search\Client;

use Acquia\Common\NoncerAbstract;

/**
 * Generates the HMAC hash used to sign Acquia Search requests.
 */
class Signature
{
    /**
     * @var string
     */
    protected $derivedKey;

    /**
     * @var \Acquia\Common\NoncerAbstract
     */
    protected $noncer;

    /**
     * @param string $derivedKey
     * @param \Acquia\Common\NoncerAbstract $noncer
     * @param int $requestTime
     */
    public function __construct($derivedKey, NoncerAbstract $noncer)
    {
        $this->derivedKey = $derivedKey;
        $this->noncer = $noncer;
    }

    /**
     * @return string
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
    public function getNonce()
    {
        return $this->noncer->getLastNonce();
    }

    /**
     * @param string $string
     * @param int $requestTime
     * @param string &$nonce
     *
     * @return string
     */
    public function generate($string, $requestTime, &$nonce = null)
    {
        $nonce = $this->noncer->generate();
        return hash_hmac('sha1', $requestTime . $nonce . $string, $this->derivedKey);
    }
}
