<?php

namespace Acquia\Rest;

abstract class SignatureAbstract
{
    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var \Acquia\Rest\NoncerInterface
     */
    private $noncer;

    /**
     * @var int
     */
    private $requestTime = 0;

    /**
     * @var int
     */
    protected $noncerLength = NoncerAbstract::DEFAULT_LENGTH;

    /**
     * @var string
     */
    protected static $defaultNoncerClass = 'Acquia\Rest\RandomStringNoncer';

    /**
     * @param string $secretKey
     */
    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    abstract public function generate($data);

    /**
     * Returns the shared secret.
     *
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

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
     * @return \Acquia\Rest\NoncerInterface
     *
     * @throws \UnexpectedValueException
     */
    public function getNoncer()
    {
        if (!isset($this->noncer)) {
            $this->noncer = new self::$defaultNoncerClass($this->noncerLength);
            if (!$this->noncer instanceof NoncerInterface) {
                throw new \UnexpectedValueException('Noncer must implement Acquia\Rest\NoncerInterface');
            }
        }
        return $this->noncer;
    }

    /**
     * @return string
     */
    public function generateNonce()
    {
        return $this->getNoncer()->generate();
    }

    /**
     * Returns the last nonce that was generated.
     *
     * @return string
     */
    public function getNonce()
    {
        return $this->getNoncer()->getLastNonce();
    }

    /**
     * @param int $requestTime
     *
     * @return \Acquia\Rest\SignatureAbstract
     */
    public function setRequestTime($requestTime)
    {
        $this->requestTime = $requestTime;
        return $this;
    }

    /**
     * @return \Acquia\Rest\SignatureAbstract
     */
    public function unsetRequestTime()
    {
        $this->requestTime = 0;
        return $this;
    }

    /**
     * @return int
     */
    public function getRequestTime()
    {
        $this->requestTime = $this->requestTime ?: time();
        return $this->requestTime;
    }
}
