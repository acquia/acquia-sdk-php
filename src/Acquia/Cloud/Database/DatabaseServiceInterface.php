<?php

namespace Acquia\Cloud\Database;

interface DatabaseServiceInterface
{
    /**
     * Returns the credentials to the active master database.
     *
     * @param string $dbName
     *
     * @throws \OutOfBoundsException
     *
     * @return \Acquia\Cloud\Database\DatabaseCredentials
     */
    public function credentials($dbName);
}
