<?php

namespace Acquia\Cloud\Api\Response;

class Task extends \ArrayObject
{
    /**
     * @param array|string $data
     */
    public function __construct($data)
    {
        if (is_string($data)) {
            $data = array('id' => $data);
        }
        parent::__construct($data);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "{$this['id']}";
    }
}
