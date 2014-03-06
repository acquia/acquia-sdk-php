<?php

namespace Acquia\Search;

use Acquia\Rest\SignatureAbstract;

/**
 * Generates the HMAC hash used to sign Acquia Search requests.
 */
class Signature extends SignatureAbstract
{
    /**
     * {@inheritdoc}
     */
    public function generate($string)
    {
        $data = $this->getRequestTime() . $this->generateNonce() . $string;
        return hash_hmac('sha1', $data, $this->getSecretKey());
    }
}
