<?php

namespace Acquia\Test\Common;

use Acquia\Common\Environment;

class DummyEnvironment extends Environment
{
    public function init()
    {
        return 'phpunit';
    }
}
