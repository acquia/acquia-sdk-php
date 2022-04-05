<?php

namespace Acquia\Rest;

class Collection extends \ArrayObject
{
    /**
     * @var \Guzzle\Http\Message\Response
     */
    protected $response;

    /**
     * @var string
     */
    protected $elementClass = '\Acquia\Rest\Element';

    /**
     * The array key containing the collection, null if it is not nested.
     *
     * Alternately set an array of keys that the may contain the collection.
     * This is useful when working with inconsistent APIs that store collections
     * of the same elements in different properties depending on the endpoint
     * that is consumed.
     *
     * @var string|array
     */
    protected $collectionProperty;

    /**
     * @param \GuzzleHttp\Psr7\Request|\Psr\Http\Message\ResponseInterface $dataSource
     */
    public function __construct($dataSource)
    {
        if (is_a($dataSource, '\GuzzleHttp\Psr7\Request')) {
            $this->response = $dataSource->send();
        } elseif (is_a($dataSource, '\Psr\Http\Message\ResponseInterface')) {
            $this->response = $dataSource;
        } else {
            throw new \InvalidArgumentException(
                sprintf("%s can't be constructed using data from %s.", get_class($this), get_class($dataSource))
            );
        }
        parent::__construct(json_decode($this->response->getBody(), TRUE));
    }

    /**
     * Keys the array of objects by their identifier, constructs and returns and
     * array object.
     *
     * When the object is cast to a string, its unique identifier is returned.
     *
     * @return \ArrayObject
     *
     * @throws \OutOfBoundsException
     *
     * @see \Acquia\Rest\Element::__toString()
     */
    public function getIterator()
    {
        $array = $this->getArrayCopy();

        // Is the collection nested in the array?
        if (isset($this->collectionProperty)) {

            // Locate the collection in the response.
            $collectionFound = false;
            $property = NULL;
            foreach ((array) $this->collectionProperty as $property) {
                if (isset($array[$property])) {
                    $collectionFound = true;
                    break;
                }
            }

            if (!$collectionFound) {
                throw new \OutOfBoundsException('Collection not found in response');
            }

            $array = $array[$property];
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
        return (string) $this->response->getBody();
    }
}
