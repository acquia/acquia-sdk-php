<?php

namespace Acquia\Cloud\Api\Response;

use Guzzle\Http\Message\Request;

class Response extends \ArrayObject
{
    /**
     * @var \Guzzle\Http\Message\Response
     */
    protected $response;

    /**
     * @var string
     */
    protected $objectClass = '\Acquia\Cloud\Api\Response\Object';

    /**
     * @param \Guzzle\Http\Message\Request $request
     *
     * @throws \RuntimeException
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
     * @see \Acquia\Cloud\Api\Response\Object::__toString()
     */
    public function getIterator()
    {
        $objects = array();
        foreach ($this->getArrayCopy() as $item) {
            $object = new $this->objectClass($item);
            $id = (string) $object;
            $objects[$id] = $object;
        }
        return new \ArrayObject($objects);
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
