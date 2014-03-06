<?php

namespace Acquia\Test\Rest;

use Acquia\Rest\ServiceManagerAware;
use Guzzle\Service\Client;

class DummyClient extends Client implements ServiceManagerAware
{
    function getBuilderParams()
    {
        return array();
    }
}
