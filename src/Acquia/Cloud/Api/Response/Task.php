<?php

namespace Acquia\Cloud\Api\Response;

use Acquia\Json\Json;

class Task extends \Acquia\Common\Element
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
    public function state()
    {
        return $this['state'];
    }

    /**
     * @return boolean
     */
    public function started()
    {
        return !empty($this['started']);
    }

    /**
     * @return string|array
     */
    public function body()
    {
        if (preg_match('/^[\[{]/', $this['body'])) {
            return Json::decode($this['body']);
        } else {
            return $this['body'];
        }

    }

    /**
     * @return boolean
     */
    public function hidden()
    {
        return !empty($this['hidden']);
    }

    /**
     * @return string
     */
    public function description()
    {
        return $this['description'];
    }

    /**
     * @return string
     */
    public function result()
    {
        return $this['result'];
    }

    /**
     * @return boolean
     */
    public function completed()
    {
        return !empty($this['completed']);
    }

    /**
     * @return \DateTime
     */
    public function created()
    {
        $created = new \DateTime();
        return $created->setTimestamp($this['created']);
    }

    /**
     * @return string
     */
    public function queue()
    {
        return $this['queue'];
    }

    /**
     * @return array
     */
    public function cookie()
    {
        return Json::decode($this['cookie']);
    }

    /**
     * @return string
     */
    public function recipient()
    {
        return $this['recipient'];
    }

    /**
     * @return string
     */
    public function sender()
    {
        return $this['sender'];
    }

    /**
     * @return percentage
     */
    public function percentage()
    {
        return $this['percentage'];
    }
}
