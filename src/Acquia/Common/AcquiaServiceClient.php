<?php

namespace Acquia\Common;

use Guzzle\Service\Client;

class AcquiaServiceClient extends Client
{
    /**
     * @var string
     */
    protected static $noncerClass = 'Acquia\Common\RandomStringNoncer';

    /**
     * @param string $class
     */
    public function setNoncerClass($class)
    {
        self::$noncerClass = $class;
    }

    /**
     * @return string
     */
    public function getNoncerClass()
    {
        return self::$noncerClass;
    }

    /**
     * @return Acquia\Common\NoncerAbstract
     */
    public static function noncerFactory()
    {
        $noncer = new self::$noncerClass();
        if (!$noncer instanceof NoncerAbstract) {
            throw new \UnexpectedValueException('Noncer must be an instance of Acquia\Common\NoncerAbstract');
        }
        return $noncer;
    }
}