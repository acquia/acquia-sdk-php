<?php

namespace Acquia\Test\Common;

use Acquia\Environment\Environment;

class DummyEnvironment extends Environment
{
    public function init()
    {
        return 'phpunit';
    }
}
