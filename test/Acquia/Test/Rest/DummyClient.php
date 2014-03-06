<?php

namespace Acquia\Test\Rest;

use Acquia\Rest\AcquiaServiceManagerAware;
use Guzzle\Service\Client;

class DummyClient extends Client implements AcquiaServiceManagerAware
{
    function getBuilderParams()
    {
        return array();
    }
}
