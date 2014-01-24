<?php

namespace Acquia\Cloud\Api\Response;

class DatabaseBackup extends Object
{
    /**
     * @var string
     */
    protected $idColumn = 'id';

    /**
     * @var string
     */
    public function checksum()
    {
        return $this['checksum'];
    }

    /**
     * @var string
     */
    public function databaseName()
    {
        return $this['name'];
    }

    /**
     * @var boolean
     */
    public function deleted()
    {
        return !empty($this['deleted']);
    }

    /**
     * @var string
     */
    public function link()
    {
        return $this['link'];
    }

    /**
     * @var \DateTime
     */
    public function started()
    {
        $created = new \DateTime();
        return $created->setTimestamp($this['started']);
    }

    /**
     * @var string
     */
    public function type()
    {
        return $this['type'];
    }

    /**
     * @var \DateTime
     */
    public function completed()
    {
        $created = new \DateTime();
        return $created->setTimestamp($this['completed']);
    }

    /**
     * @var string
     */
    public function path()
    {
        return $this['path'];
    }

    /**
     * @var string
     */
    public function id()
    {
        return $this['id'];
    }
}