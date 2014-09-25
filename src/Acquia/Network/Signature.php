<?php

namespace Acquia\Network;

use Acquia\Rest\SignatureAbstract;

/**
 * Generates the HMAC hash used to sign Acquia Network requests.
 */
class Signature extends SignatureAbstract
{
    /**
     * {@inheritdoc}
     */
    public function generate($params = array())
    {
         if (!is_array($params)) {
             throw new \UnexpectedValueException('Expecting data to be an array.');
         }

         $time = $this->getRequestTime();
         $nonce = $this->generateNonce();
         $key = $this->getSecretKey();

         if (empty($params['rpc_version']) || $params['rpc_version'] < 2) {
              $string = $time . ':' . $nonce . ':' . $key . ':' . serialize($params);

              return base64_encode(
                  pack("H*", sha1((str_pad($key, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) .
                  pack("H*", sha1((str_pad($key, 64, chr(0x00)) ^ (str_repeat(chr(0x36), 64))) .
                  $string)))));
        } elseif ($params['rpc_version'] == 2) {
              $string = $time . ':' . $nonce . ':' . json_encode($params);
              return sha1((str_pad($key, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) . pack("H*", sha1((str_pad($key, 64, chr(0x00)) ^ (str_repeat(chr(0x36), 64))) . $string)));
        } else {
              $string = $time . ':' . $nonce;
              return sha1((str_pad($key, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) . pack("H*", sha1((str_pad($key, 64, chr(0x00)) ^ (str_repeat(chr(0x36), 64))) . $string)));
        }
    }
}
