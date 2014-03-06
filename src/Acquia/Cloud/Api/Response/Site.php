<?php

namespace Acquia\Cloud\Api\Response;

class Site extends \Acquia\Rest\Element
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
    public function vcsUrl()
    {
        return $this['vcs_url'];
    }

    /**
     * @return string
     */
    public function uuid()
    {
        return $this['uuid'];
    }

    /**
     * @return string
     */
    public function unixUsername()
    {
        return $this['unix_username'];
    }

    /**
     * @return string
     */
    public function title()
    {
        return $this['title'];
    }

    /**
     * @return string
     */
    public function vcsType()
    {
        return $this['vcs_type'];
    }

    /**
     * @return boolean
     */
    public function productionMode()
    {
        return !empty($this['production_mode']);
    }
}
