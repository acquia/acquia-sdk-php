<?php

namespace Acquia\Cloud\Api\Response;

class Tasks extends \ArrayObject
{
    /**
     * @param array $tasks
     */
    public function __construct($tasks)
    {
        foreach ($tasks as $task) {
            $this[$task['id']] = new Task($task);
        }
    }
}
