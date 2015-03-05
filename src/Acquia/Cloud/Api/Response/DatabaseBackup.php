<?php

namespace Acquia\Cloud\Api\Response;

class DatabaseBackup extends \Acquia\Rest\Element
{
    /**
     * @var string
     */
    protected $idColumn = 'id';

    /**
     * @return string
     */
    public function checksum()
    {
        return $this['checksum'];
    }

    /**
     * @return string
     */
    public function databaseName()
    {
        return $this['name'];
    }

    /**
     * @return boolean
     */
    public function deleted()
    {
        return !empty($this['deleted']);
    }

    /**
     * @return string
     */
    public function link()
    {
        return $this['link'];
    }

    /**
     * @return \DateTime
     */
    public function started()
    {
        $created = new \DateTime();
        return $created->setTimestamp($this['started']);
    }

    /**
     * @return string
     */
    public function type()
    {
        return $this['type'];
    }

    /**
     * @return \DateTime
     */
    public function completed()
    {
        $created = new \DateTime();
        return $created->setTimestamp($this['completed']);
    }

    /**
     * @return string
     */
    public function path()
    {
        return $this['path'];
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this['id'];
    }
}
