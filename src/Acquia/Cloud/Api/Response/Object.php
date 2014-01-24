<?php

namespace Acquia\Cloud\Api\Response;

use Guzzle\Http\Message\Request;

class Object extends \ArrayObject
{
    /**
     * @var string
     */
    protected $idColumn = 'name';

    /**
     * @param string|array|\Guzzle\Http\Message\Request $array
     */
    public function __construct($array)
    {
        if ($array instanceof Request) {
            $array = $array->send()->json();
        } elseif (is_string($array)) {
            $array = array($this->idColumn => $array);
        }
        parent::__construct($array);
    }

    /**
     * @param string $idColumn
     *
     * @return \Acquia\Cloud\Api\Response\Object
     */
    public function setIdColumn($idColumn)
    {
        $this->idColumn = $idColumn;
        return $this;
    }

    /**
     * @return string|int
     */
    public function getIdColumn()
    {
        return $this->idColumn;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this[$this->idColumn];
    }
}
