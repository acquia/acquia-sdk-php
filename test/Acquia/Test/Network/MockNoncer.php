<?php

namespace Acquia\Test\Network;

use Acquia\Rest\NoncerAbstract;

class MockNoncer extends NoncerAbstract
{
    public function hash()
    {
        return 'mock-hash';
    }
}
