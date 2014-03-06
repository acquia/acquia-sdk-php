<?php

namespace Acquia\Json;

interface JsonInterface
{
    /**
     * @param mixed $data
     *
     * @return string
     */
    public static function encode($data);

    /**
     * @param string $json
     *
     * @return array
     */
    public static function decode($json);

}
