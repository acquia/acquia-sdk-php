<?php

namespace Acquia\Common;

class Json
{
    /**
     * @param mixed $data
     *
     * @return string
     */
    public static function encode($data)
    {
        $options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
        if (defined('JSON_PRETTY_PRINT')) {
            $options = $options | JSON_PRETTY_PRINT;
        }
        if (defined('JSON_UNESCAPED_SLASHES')) {
            $options = $options | JSON_UNESCAPED_SLASHES;
        }

        return self::pretty_print(json_encode($data, $options));
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
     * JSON_PRETTY_PRINT option is not available until PHP 5.4
     *
     * @param string $json The original JSON string to process.
     *
     * @return string Indented version of the original JSON string.
     */
    public static function pretty_print($json) {

        $result = '';
        $pos = 0;
        $string_length = strlen($json);
        $indentation = '    ';
        $newline = "\n";
        $previous_char = '';
        $out_of_quotes = true;

        // JSON_UNESCAPED_SLASHES is also not available until PHP 5.4
        if (!defined('JSON_UNESCAPED_SLASHES') && strpos($json, '/')) {
            $json = preg_replace('#\134{1}/#', '/', $json);
        }

        // If there are already newlines, assume formatted
        if (strpos($json, $newline)) {
            return $json;
        }

        for ($i=0; $i<=$string_length; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            if ($previous_char == ':' && $out_of_quotes) {
                $result .= ' ';
            }

            // Are we inside a quoted string?
            if ($char == '"' && $previous_char != '\\') {
                $out_of_quotes = !$out_of_quotes;

                // If this character is the end of an element,
                // output a new line and indent the next line.
            } else if(($char == '}' || $char == ']') && $out_of_quotes) {
                $result .= $newline;
                $pos --;
                for ($j=0; $j<$pos; $j++) {
                    $result .= $indentation;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $out_of_quotes) {
                $result .= $newline;
                if ($char == '{' || $char == '[') {
                    $pos ++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentation;
                }
            }

            $previous_char = $char;
        }

        return $result;
    }

}
