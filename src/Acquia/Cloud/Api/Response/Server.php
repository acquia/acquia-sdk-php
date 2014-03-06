<?php

namespace Acquia\Cloud\Api\Response;

class Server extends \Acquia\Rest\Element
{
    /**
     * @return array
     */
    public function services()
    {
        return $this['services'];
    }

    /**
     * @return string
     */
    public function availabilityZone()
    {
        return $this['ec2_availability_zone'];
    }

    /**
     * @return string
     */
    public function region()
    {
        return $this['ec2_region'];
    }

    /**
     * @return string
     */
    public function amiType()
    {
        return $this['ami_type'];
    }

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
    public function fqdn()
    {
        return $this['fqdn'];
    }
}
