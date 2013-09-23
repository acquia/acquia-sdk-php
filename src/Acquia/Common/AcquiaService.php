<?php

namespace Acquia\Common;

use Guzzle\Service\Builder\ServiceBuilder;

class AcquiaService extends ServiceBuilder
{
    /**
     * @return string
     */
    public function asJson($filename = null)
    {
        $data = array(
            'class' => get_class($this),
            'services' => $this->builderConfig,
        );

        $options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
        if (defined('JSON_PRETTY_PRINT')) {
            $options = $options | JSON_PRETTY_PRINT;
        }
        if (defined('JSON_UNESCAPED_SLASHES')) {
            $options = $options | JSON_UNESCAPED_SLASHES;
        }

        $json = json_encode($data, $options);

        if ($filename !== null) {
            file_put_contents($filename, $json);
        }

        return $json;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->asJson();
    }
}
