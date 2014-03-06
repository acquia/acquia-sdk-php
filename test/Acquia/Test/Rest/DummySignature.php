<?php

namespace Acquia\Test\Rest;

use Acquia\Rest\SignatureAbstract;

class DummySignature extends SignatureAbstract
{
    public function generate($data)
    {
        return $data;
    }
}
