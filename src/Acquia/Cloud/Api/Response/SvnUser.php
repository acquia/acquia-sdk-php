<?php

namespace Acquia\Cloud\Api\Response;

class SvnUser extends Object
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
    public function username()
    {
        return $this['username'];
    }
}
