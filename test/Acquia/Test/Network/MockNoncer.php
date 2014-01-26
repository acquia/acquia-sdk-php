<?php

namespace Acquia\Test\Network;

use Acquia\Common\NoncerAbstract;

class MockNoncer extends NoncerAbstract
{
    public function hash()
    {
        return 'mock-hash';
    }
}
