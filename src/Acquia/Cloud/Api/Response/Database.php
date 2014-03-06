<?php

namespace Acquia\Cloud\Api\Response;

class Database extends \Acquia\Rest\Element
{
    /**
     * @return string
     */
    public function name()
    {
        return $this['name'];
    }

    /**
     * @return string
     */
    public function username()
    {
        return $this['username'];
    }

    /**
     * @return string
     */
    public function instanceName()
    {
        return $this['instance_name'];
    }

    /**
     * @return string
     */
    public function password()
    {
        return $this['password'];
    }

    /**
     * @return string
     */
    public function dbCluster()
    {
        return $this['db_cluster'];
    }

    /**
     * @return string
     */
    public function host()
    {
        return $this['host'];
    }
}
