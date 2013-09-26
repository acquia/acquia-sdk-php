<?php

namespace Acquia\Common;

use Guzzle\Service\Client;

class AcquiaClient extends Client
{
    /**
     * @var string
     */
    protected static $noncerClass = 'Acquia\Common\RandomStringNoncer';

    /**
     * @param string $class
     */
    public static function setNoncerClass($class)
    {
        self::$noncerClass = $class;
    }

    /**
     * @return string
     */
    public static function getNoncerClass()
    {
        return self::$noncerClass;
    }

    /**
     * @param int $length
     *
     * @return Acquia\Common\NoncerAbstract
     */
    public static function noncerFactory($length = NoncerAbstract::DEFAULT_LENGTH)
    {
        $noncer = new self::$noncerClass($length);
        if (!$noncer instanceof NoncerAbstract) {
            throw new \UnexpectedValueException('Noncer must be an instance of Acquia\Common\NoncerAbstract');
        }
        return $noncer;
    }

    /**
     * @return string
     */
    public function getBuilderClass()
    {
        return 'Acquia\Common\AcquiaService';
    }

    /**
     * @return array
     */
    public function getBuilderParams()
    {
        return array();
    }

    /**
     * @param string $filename
     * @param string $name
     *
     * @todo Implement write locking
     */
    public function addToService($filename, $name)
    {
        $builderClass = $this->getBuilderClass();

        /* @var \Acquia\Common\AcquiaService $builder */
        if (file_exists($filename)) {

            $builder = $builderClass::factory($filename);
            $builder->set($name, array(
                'class' => get_class($this),
                'params' => $this->getBuilderParams(),
            ));

        } else {

            $builder = $builderClass::factory(array(
                'class' => $builderClass,
                'services' => array(
                    $name => array(
                        'class' => get_class($this),
                        'params' => $this->getBuilderParams(),
                    ),
                ),
            ));

        }

        $builder->asJson($filename);
    }
}
