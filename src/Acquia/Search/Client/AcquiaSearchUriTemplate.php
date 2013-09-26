<?php

namespace Acquia\Search\Client;

use Guzzle\Parser\UriTemplate\UriTemplate;

class AcquiaSearchUriTemplate extends UriTemplate
{
    /**
     * {@inheritdoc}
     *
     * The Guzzle docs say to change the regex for Solr, however the method to
     * do so does not exist and everything is private. Therefore we are
     * handcuffed unless we want to fork the library or copy whole class.
     *
     * @see http://guzzlephp.org/http-client/uri-templates.html
     * @see http://wiki.apache.org/solr/LocalParams
     * @see https://github.com/guzzle/guzzle-docs/pull/56
     */
    public function expand($template, array $variables)
    {
        return str_replace('{+base_path}', $variables['base_path'], $template);
    }
}
