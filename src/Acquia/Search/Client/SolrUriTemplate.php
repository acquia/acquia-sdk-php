<?php

namespace Acquia\Search\Client;

use Guzzle\Parser\UriTemplate\UriTemplate;

class SolrUriTemplate extends UriTemplate
{
    /**
     * {@inheritdoc}
     *
     * Don't expand URI template expressions.
     *
     * @see http://guzzlephp.org/http-client/uri-templates.html
     */
    public function expand($template, array $variables)
    {
        return $template;
    }
}
