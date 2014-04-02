<?php

namespace Acquia\Json;

class Json implements JsonInterface
{
    /**
     * @var boolean
     */
    protected static $useNativePrettyPrint = true;

    /**
     * Use the native PHP pretty print options. Set to false to use the local
     * pretty print method in this class (not recommended).
     *
     * @param boolean $useNative
     */
    public static function useNativePrettyPrint($useNative = true)
    {
        self::$useNativePrettyPrint = $useNative;
    }

    /**
     * @param mixed $data
     * @param bool $prettyPrint
     *
     * @return string
     */
    public static function encode($data, $prettyPrint = true)
    {
        $options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

        $useNative = self::$useNativePrettyPrint && defined('JSON_PRETTY_PRINT') && defined('JSON_UNESCAPED_SLASHES');
        if ($prettyPrint && $useNative) {
            $options = $options | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
        }

        $json = json_encode($data, $options);
        if ($prettyPrint && !$useNative) {
            $json = self::prettyPrint($json);
        }

        return $json;
    }

    /**
     * @param string $json
     *
     * @return array
     */
    public static function decode($json)
    {
        return json_decode($json, true);
    }

    /**
     * Indents a flat JSON string to make it more human-readable.
     *
     * The JSON_PRETTY_PRINT and JSON_UNESCAPED_SLASHES options are not
     * available until PHP 5.4.
     *
     * @param string $json
     *
     * @return string
     */
    protected static function prettyPrint($json)
    {
        $result = '';
        $pos = 0;
        $indentation = '    ';
        $newline = "\n";
        $previousChar = '';
        $outOfQuotes = true;

        // Unescape slashes.
        if (strpos($json, '/')) {
            $json = preg_replace('#\134{1}/#', '/', $json);
        }

        $stringLength = strlen($json);

        for ($i = 0; $i <= $stringLength; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            if (':' == $previousChar && $outOfQuotes) {
                $result .= ' ';
            }

            // Are we inside a quoted string?
            if ('"' == $char && $previousChar != '\\') {
                $outOfQuotes = !$outOfQuotes;

                // If this character is the end of an element,
                // output a new line and indent the next line.
            } elseif (('}' == $char || ']' == $char) && $outOfQuotes) {
                $result .= $newline;
                $pos --;
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentation;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if ((',' == $char || '{' == $char || '[' == $char) && $outOfQuotes) {
                $result .= $newline;
                if ('{' == $char || '[' == $char) {
                    $pos ++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentation;
                }
            }

            $previousChar = $char;
        }

        return $result;
    }

    /**
     * Parses a file into a PHP array.
     *
     * @param string $filepath
     *
     * @throws \RuntimeException
     */
    public static function parseFile($filepath)
    {
        if (!is_file($filepath)) {
            throw new \RuntimeException('File not found: ' . $filepath);
        }

        if (!$filedata = static::readFiledata($filepath)) {
            throw new \RuntimeException('Error reading file: ' . $filepath);
        }

        if (!$json = self::decode($filedata)) {
            throw new \RuntimeException('Error parsing json: ' . $filepath);
        }

        return $json;
    }

    /**
     * Wrapper around file_get_contents(), useful for testing the inability to
     * read a file.
     *
     * @param string $filepath
     *
     * @return string|false
     */
    protected static function readFiledata($filepath)
    {
        return @file_get_contents($filepath);
    }
}
