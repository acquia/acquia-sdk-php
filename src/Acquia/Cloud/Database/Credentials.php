<?php

namespace Acquia\Cloud\Database;

class Credentials extends \ArrayObject
{
    /**
     * @return int
     */
    public function id()
    {
        return $this['id'];
    }

    /**
     * @return int
     */
    public function clusterId()
    {
        return $this['db_cluster_id'];
    }

    /**
     * @return string
     */
    public function role()
    {
        return $this['role'];
    }

    /**
     * @return string
     */
    public function databaseName()
    {
        return $this['name'];
    }

    /**
     * @return string
     */
    public function username()
    {
        return $this['user'];
    }

    /**
     * @return string
     */
    public function password()
    {
        return $this['pass'];
    }

    /**
     * @return string
     */
    public function port()
    {
        return $this['port'];
    }

    /**
     * @return string
     */
    public function host()
    {
        return $this['host'];
    }

    /**
     * @return array
     */
    public function urls()
    {
        return $this['db_url_ha'];
    }

    /**
     * @return string
     */
    public function activeUrl()
    {
        $host = $this->host();
        $urls = $this->urls();
        return $urls[$host];
    }

    /**
     * Returns the DSN for the active host.
     *
     * @return string
     */
    public function dsn()
    {
        return 'mysql:dbname=' . $this->databaseName() . ';host=' . $this->host() . ';port=' . $this->port();
    }

    /**
     * Returns the DSN for the active host.
     */
    public function __toString()
    {
        return $this->dsn();
    }
}
