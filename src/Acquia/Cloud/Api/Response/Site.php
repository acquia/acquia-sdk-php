<?php

namespace Acquia\Cloud\Api\Response;

class Site extends \ArrayObject
{
    /**
     * @param array|string $data
     */
    public function __construct($data)
    {
        if (is_string($data)) {
            $data = array('name' => $data);
        }
        parent::__construct($data);
        list($this['hosting_stage'], $this['site_group']) = explode(':', $data['name']);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this['name'];
    }
}
