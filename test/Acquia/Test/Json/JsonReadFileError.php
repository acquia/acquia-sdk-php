<?php

namespace Acquia\Test\Json;

use Acquia\Json\Json;

class JsonReadFileError extends Json
{
    static protected function readFiledata($filepath)
    {
        return false;
    }
}
