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
    protected function id()
    {
        return $this['id'];
    }

    /**
     * @return string
     */
    protected function name()
    {
        return $this['name'];
    }
}
