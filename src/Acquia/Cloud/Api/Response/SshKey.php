<?php

namespace Acquia\Cloud\Api\Response;

class SshKey extends \Acquia\Rest\Element
{
    /**
     * @var string
     */
    protected $idColumn = 'id';

    /**
     * @return string
     */
    public function id()
    {
        return $this['id'];
    }

    /**
     * @return string
     */
    public function publicKey()
    {
        return $this['ssh_pub_key'];
    }

    /**
     * @return string
     */
    public function nickname()
    {
        return $this['nickname'];
    }
}
