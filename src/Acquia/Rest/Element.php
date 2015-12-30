<?php

namespace Acquia\Rest;

class Element extends \ArrayObject
{
    /**
     * @var string
     */
    protected $idColumn = 'name';

    /**
     * @param string|array|\GuzzleHttp\Psr7\Request|\Psr\Http\Message\ResponseInterface $dataSource
     */
    public function __construct($dataSource)
    {
        if (is_a($dataSource, '\GuzzleHttp\Psr7\Request')) {
            $array = json_decode($dataSource->send()->getBody(), TRUE);
        } elseif (is_a($dataSource, '\Psr\Http\Message\ResponseInterface')) {
            $array = json_decode($dataSource->getBody(), TRUE);
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
