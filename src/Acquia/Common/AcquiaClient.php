<?php

namespace Acquia\Common;

use Guzzle\Service\Client;

class AcquiaClient extends Client
{
    /**
     * @var \Acquia\Common\NoncerAbstract
     */
    private $noncer;

    /**
     * @var int
     */
    protected $noncerLength = NoncerAbstract::DEFAULT_LENGTH;

    /**
     * @var string
     */
    protected static $defaultNoncerClass = 'Acquia\Common\RandomStringNoncer';

    /**
     * @param string $class
     */
    public static function setDefaultNoncerClass($class)
    {
        self::$defaultNoncerClass = $class;
    }

    /**
     * @return string
     */
    public static function getDefaultNoncerClass()
    {
        return self::$defaultNoncerClass;
    }

    /**
     * Returns a noncer, instantiates it if it doesn't exist.
     *
     * @return \Acquia\Common\NoncerAbstract
     *
     * @throws \UnexpectedValueException
     */
    public function getNoncer()
    {
        if (!isset($this->noncer)) {
            $this->noncer = new self::$defaultNoncerClass($this->noncerLength);
            if (!$this->noncer instanceof NoncerAbstract) {
                throw new \UnexpectedValueException('Noncer must be an instance of Acquia\Common\NoncerAbstract');
            }
        }
        return $this->noncer;
    }

    /**
     * @return array
     */
    public function getBuilderParams()
    {
        return array();
    }
}
