<?php

namespace Acquia\Common;

use Guzzle\Http\Message\Request;

class Collection extends \ArrayObject
{
    /**
     * @var \Guzzle\Http\Message\Response
     */
    protected $response;

    /**
     * @var string
     */
    protected $elementClass = '\Acquia\Common\Element';

    /**
     * The array key containing the collection, null if it is not nested.
     *
     * @var string
     */
    protected $collectionProperty;

    /**
     * @param \Guzzle\Http\Message\Request $request
     */
    public function __construct(Request $request)
    {
        $this->response = $request->send();
        parent::__construct($this->response->json());
    }

    /**
     * Keys the array of objects by their identifier, constructs and returns and
     * array object.
     *
     * When the object is cast to a string, its unique identifier is returned.
     *
     * @return \ArrayObject
     *
     * @see \Acquia\Common\Element::__toString()
     */
    public function getIterator()
    {
        $array = $this->getArrayCopy();

        // Is the collection nested in the array?
        if (isset($this->collectionProperty) && isset($array[$this->collectionProperty])) {
            $array = $array[$this->collectionProperty];
        }

        // Build the collection.
        $collection = array();
        foreach ($array as $item) {
            $element = new $this->elementClass($item);
            $collection[(string) $element] = $element;
        }

        return new \ArrayObject($collection);
    }

    /**
     * Returns the raw response body, usually a string containing JSON.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->response->getBody(true);
    }
}
