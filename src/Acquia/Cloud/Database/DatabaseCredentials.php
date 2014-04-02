<?php

namespace Acquia\Cloud\Database;

class DatabaseCredentials extends \ArrayObject
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
     *
     * @throws \OutOfBoundsException
     */
    public function dsn()
    {
        // @see https://github.com/acquia/acquia-sdk-php/issues/27
        if (!isset($this['name'])) {
            throw new \OutOfBoundsException('Malformed response: expecting "name" property');
        }
        if (!isset($this['host'])) {
            throw new \OutOfBoundsException('Malformed response: expecting "host" property');
        }
        if (!isset($this['port'])) {
            throw new \OutOfBoundsException('Malformed response: expecting "port" property');
        }

        return 'mysql:dbname=' . $this['name'] . ';host=' . $this['host'] . ';port=' . $this['port'];
    }

    /**
     * Returns the DSN for the active host.
     */
    public function __toString()
    {
        return $this->dsn();
    }
}
