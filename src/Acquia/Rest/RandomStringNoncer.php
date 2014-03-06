<?php

namespace Acquia\Rest;

/**
 * Returns a random string of ASCII characters.
 */
class RandomStringNoncer extends NoncerAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function hash()
    {
        $string = '';
        for ($i = 0; $i < $this->length; $i++) {
            $string .= chr(mt_rand(32, 126));
        }
        return base64_encode($string);
    }
}
