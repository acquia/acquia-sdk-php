<?php

namespace Acquia\Rest;

interface ServiceManagerAware
{
    /**
     * Returns the parameters that can be used by the service manager to
     * instantiate the client.
     */
    public function getBuilderParams();
}
