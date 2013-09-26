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
        $json = Json::encode(array(
            'class' => get_class($this),
            'services' => $this->builderConfig,
        ));

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
