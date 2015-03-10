<?php

namespace Acquia\Cloud\Api\Response;

class SshKey extends \Acquia\Rest\Element
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
    public function publicKey()
    {
        return $this['ssh_pub_key'];
    }

    /**
     * @return string
     */
    public function nickname()
    {
        return $this['nickname'];
    }

    /**
     * Indicates whether this key has access to the shell.
     *
     * @return bool
     *   TRUE if this key can access the shell; FALSE otherwise.
     */
    public function shellAccess()
    {
        return $this['shell_access'];
    }

    /**
     * Indicates whether this key has access to the VCS repository.
     *
     * @return bool
     *   TRUE if this key can access the VCS repository; FALSE otherwise.
     */
    public function vcsAccess()
    {
        return $this['vcs_access'];
    }

    /**
     * Indicates the names of the environments this key does not have access to.
     *
     * @return string[]
     *   An array of environment names this key cannot access.
     */
    public function blacklist()
    {
        return $this['blacklist'];
    }
}
