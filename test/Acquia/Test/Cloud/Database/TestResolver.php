<?php

namespace Acquia\Test\Cloud\Database;

class TestResolver extends \Net_DNS2_Resolver
{
    /**
     * @var bool
     */
    protected $throwException = false;

    public function __construct()
    {
        $options = array('nameservers' => array('127.0.0.1', 'dns-master'));
        parent::__construct($options);
    }

    public function throwException($throw = true)
    {
        $this->throwException = (bool) $throw;
    }

    public function query($name, $type = 'A', $class = 'IN')
    {
        if ($this->throwException) {
            throw new \Net_DNS2_Exception('Test exception');
        }

        $response = new \stdClass();
        $response->answer = array(
            (object) array('cname' => 'staging-123'),
        );
        return $response;
    }
}
