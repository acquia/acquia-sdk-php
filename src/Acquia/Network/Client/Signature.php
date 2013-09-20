<?php

namespace Acquia\Network\Client;

use Acquia\Common\NoncerAbstract;

class Signature
{
    /**
     * @var string
     */
    protected $acquiaId;

    /**
     * @var string
     */
    protected $acquiaKey;

    /**
     * @var \Acquia\Common\NoncerAbstract
     */
    protected $noncer;

    /**
     * @var int
     */
    protected $requestTime = 0;

    /**
     * @param string $acquiaId
     * @param string $acquiaKey
     */
    public function __construct($acquiaId, $acquiaKey, NoncerAbstract $noncer)
    {
        $this->acquiaId = $acquiaId;
        $this->acquiaKey = $acquiaKey;
        $this->noncer = $noncer;
    }

    /**
     * @return string
     */
    public function getAcquiaId()
    {
        return $this->acquiaId;
    }

    /**
     * @return string
     */
    public function getAcquiaKey()
    {
        return $this->acquiaKey;
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
    public function nonce()
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
              $string = $time . ':' . $nonce . ':' . $this->acquiaKey . ':' . serialize($params);

              return base64_encode(
                  pack("H*", sha1((str_pad($this->acquiaKey, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) .
                  pack("H*", sha1((str_pad($this->acquiaKey, 64, chr(0x00)) ^ (str_repeat(chr(0x36), 64))) .
                  $string)))));
        } elseif ($params['rpc_version'] == 2) {
              $string = $time . ':' . $nonce . ':' . json_encode($params);
              return sha1((str_pad($this->acquiaKey, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) . pack("H*", sha1((str_pad($this->acquiaKey, 64, chr(0x00)) ^ (str_repeat(chr(0x36), 64))) . $string)));
        } else {
              $string = $time . ':' . $nonce;
              return sha1((str_pad($this->acquiaKey, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) . pack("H*", sha1((str_pad($this->acquiaKey, 64, chr(0x00)) ^ (str_repeat(chr(0x36), 64))) . $string)));
        }
    }
}
