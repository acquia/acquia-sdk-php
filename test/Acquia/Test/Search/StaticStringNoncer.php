<?php

namespace Acquia\Test\Search;

use Acquia\Rest\NoncerAbstract;

/**
 * Returns a random string of ASCII characters.
 */
class StaticStringNoncer extends NoncerAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function hash()
    {
        return '12345678';
    }
}
