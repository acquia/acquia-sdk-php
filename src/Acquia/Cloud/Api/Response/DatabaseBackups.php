<?php

namespace Acquia\Cloud\Api\Response;

class DatabaseBackups extends \Acquia\Common\Collection
{
    /**
     * @var string
     */
    protected $elementClass = '\Acquia\Cloud\Api\Response\DatabaseBackup';
}
