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

    /**
     * @param string $filepath
     *
     * @return array
     *
     * @throws \RuntimeException
     */
    public static function parseFile($filepath);
}
