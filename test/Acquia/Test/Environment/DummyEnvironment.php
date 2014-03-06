<?php

namespace Acquia\Test\Environment;

use Acquia\Environment\Environment;

class DummyEnvironment extends Environment
{
    public function init()
    {
        return 'phpunit';
    }
}
