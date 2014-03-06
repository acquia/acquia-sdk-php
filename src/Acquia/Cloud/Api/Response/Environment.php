<?php

namespace Acquia\Cloud\Api\Response;

class Environment extends \Acquia\Rest\Element
{
    /**
     * @return array
     */
    public function dbClusters()
    {
        return $this['db_clusters'];
    }

    /**
     * @return string
     */
    public function defaultDomain()
    {
        return $this['default_domain'];
    }

    /**
     * @return string
     */
    public function sshHost()
    {
        return $this['ssh_host'];
    }

    /**
     * @return string
     */
    public function vcsPath()
    {
        return $this['vcs_path'];
    }

    /**
     * @return boolean
     */
    public function liveDev()
    {
        return ('enabled' === $this['livedev']);
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this['name'];
    }
}
