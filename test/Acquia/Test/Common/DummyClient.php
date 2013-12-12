<?php

namespace Acquia\Test\Common;

use Acquia\Common\AcquiaServiceManagerAware;
use Guzzle\Service\Client;

class DummyClient extends Client implements AcquiaServiceManagerAware
{
    function getBuilderParams()
    {
        return array();
    }
}
