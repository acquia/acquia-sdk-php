<?php

namespace Acquia\Common;

abstract class NoncerAbstract
{
    const DEFAULT_LENGTH = 24;

    /**
     * @var string
     */
    protected $lastNonce = '';

    /**
     * @var int
     */
    protected $length;

    /**
     * @var string $length
     */
    public function __construct($length = self::DEFAULT_LENGTH)
    {
        $this->length = $length;
    }

    /**
     * @return string
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Generates the nonce.
     *
     * @return string
     */
    abstract protected function hash();

    /**
     * Generates and stores a nonce.
     *
     * @return string
     */
    public function generate()
    {
        $this->lastNonce = $this->hash();
        return $this->lastNonce;
    }

    /**
     * @return string
     */
    public function getLastNonce()
    {
        return $this->lastNonce;
    }

    /**
     * Generates and returns a nonce.
     *
     * @see NoncerAbstract::generate()
     */
    public function __toString()
    {
        return $this->generate();
    }
}
