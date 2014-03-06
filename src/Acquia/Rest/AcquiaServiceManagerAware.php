<?php

namespace Acquia\Rest;

interface AcquiaServiceManagerAware
{
    /**
     * Returns the parameters that can be used by the service manager to
     * instantiate the client.
     */
    public function getBuilderParams();
}
