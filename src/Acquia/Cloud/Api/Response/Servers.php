<?php

namespace Acquia\Cloud\Api\Response;

class Servers extends \Acquia\Rest\Collection
{
    /**
     * @var string
     */
    protected $elementClass = '\Acquia\Cloud\Api\Response\Server';
}
