<?php

namespace Acquia\Network\Client;

use Acquia\Common\NoncerAbstract;

class Signature
{
    /**
     * @var string
     */
    protected $networkId;

    /**
     * @var string
     */
    protected $networkKey;

    /**
     * @var \Acquia\Common\NoncerAbstract
     */
    protected $noncer;

    /**
     * @var int
     */
    protected $requestTime = 0;

    /**
     * @param string $networkId
     * @param string $networkKey
     */
    public function __construct($networkId, $networkKey, NoncerAbstract $noncer)
    {
        $this->networkId = $networkId;
        $this->networkKey = $networkKey;
        $this->noncer = $noncer;
    }

    /**
     * @return string
     */
    public function getNetworkId()
    {
        return $this->networkId;
    }

    /**
     * @return string
     */
    public function getNetworkKey()
    {
        return $this->networkKey;
    }

    /**
     * @return \Acquia\Common\NoncerAbstract
     */
    public function getNoncer()
    {
        return $this->noncer;
    }

    /**
     * @param int $requestTime
     *
     * @return \Acquia\Network\Client\Signature
     */
    public function setRequestTime($requestTime)
    {
        $this->requestTime = $requestTime;
        return $this;
    }

    /**
     * @return \Acquia\Network\Client\Signature
     */
    public function unsetRequestTime()
    {
        $this->requestTime = 0;
        return $this;
    }

    /**
     * @return int
     */
    public function getRequestTime()
    {
        return $this->requestTime ?: time();
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
     * @param array $params
     *
     * @return string
     */
    public function generate(array $params = array())
    {
         $time = $this->getRequestTime();
         $nonce = $this->noncer->generate();

         if (empty($params['rpc_version']) || $params['rpc_version'] < 2) {
              $encoded_params = serialize($params);
              $string = $time . ':' . $nonce . ':' . $this->networkKey . ':' . serialize($params);

              return base64_encode(
                  pack("H*", sha1((str_pad($this->networkKey, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) .
                  pack("H*", sha1((str_pad($this->networkKey, 64, chr(0x00)) ^ (str_repeat(chr(0x36), 64))) .
                  $string)))));
        } elseif ($params['rpc_version'] == 2) {
              $string = $time . ':' . $nonce . ':' . json_encode($params);
              return sha1((str_pad($this->networkKey, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) . pack("H*", sha1((str_pad($this->networkKey, 64, chr(0x00)) ^ (str_repeat(chr(0x36), 64))) . $string)));
        } else {
              $string = $time . ':' . $nonce;
              return sha1((str_pad($this->networkKey, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) . pack("H*", sha1((str_pad($this->networkKey, 64, chr(0x00)) ^ (str_repeat(chr(0x36), 64))) . $string)));
        }
    }
}
