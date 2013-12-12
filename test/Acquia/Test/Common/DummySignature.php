<?php

namespace Acquia\Test\Common;

use Acquia\Common\SignatureAbstract;

class DummySignature extends SignatureAbstract
{
    public function generate($data)
    {
        return $data;
    }
}
