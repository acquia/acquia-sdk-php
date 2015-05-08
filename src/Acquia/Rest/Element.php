<?php

namespace Acquia\Rest;

class Element extends \ArrayObject
{
    /**
     * @var string
     */
    protected $idColumn = 'name';

    /**
     * @param string|array|\Guzzle\Http\Message\RequestInterface|\GuzzleHttp\Message\Response $dataSource
     */
    public function __construct($dataSource)
    {
        if (is_a($dataSource, '\Guzzle\Http\Message\RequestInterface')) {
            $array = $dataSource->send()->json();
        } elseif (is_a($dataSource, '\GuzzleHttp\Message\Response')) {
            $array = $dataSource->json();
        } elseif (is_string($dataSource)) {
            $array = array($this->idColumn => $dataSource);
        } else {
            $array = (array) $dataSource;
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
