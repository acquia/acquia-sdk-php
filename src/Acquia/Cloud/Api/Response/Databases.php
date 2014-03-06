<?php

namespace Acquia\Cloud\Api\Response;

class Databases extends \Acquia\Rest\Collection
{
    /**
     * @var string
     */
    protected $elementClass = '\Acquia\Cloud\Api\Response\Database';
}
