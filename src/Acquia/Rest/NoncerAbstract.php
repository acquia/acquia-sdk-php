<?php

namespace Acquia\Rest;

abstract class NoncerAbstract implements NoncerInterface
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
     * @param integer $length
     */
    public function __construct($length = self::DEFAULT_LENGTH)
    {
        $this->length = $length;
    }

    /**
     * @param int $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @return integer
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
