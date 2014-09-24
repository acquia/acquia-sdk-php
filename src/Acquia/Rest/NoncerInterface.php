<?php

namespace Acquia\Rest;

interface NoncerInterface
{
    /**
     * Generates and stores a nonce.
     *
     * @return string
     */
    public function generate();

    /**
     * @return string
     */
    public function getLastNonce();

    /**
     * Generates and returns a nonce.
     *
     * @see NoncerAbstract::generate()
     * @return string
     */
    public function __toString();
}
