<?php

namespace Acquia\Rest;

use Guzzle\Http\Message\Request;

class Element extends \ArrayObject
{
    /**
     * @var string
     */
    protected $idColumn = 'name';

    /**
     * @param string|array|\Guzzle\Http\Message\Request $array
     */
    public function __construct($data)
    {
        if ($data instanceof Request) {
            $array = $data->send()->json();
        } elseif (is_string($data)) {
            $array = array($this->idColumn => $data);
        } else {
            $array = (array) $data;
        }
        parent::__construct($array);
    }

    /**
     * @param string $idColumn
     *
     * @return \Acquia\Rest\Element
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
