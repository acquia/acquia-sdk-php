<?php

namespace Acquia\Network;

class XmlrpcResponse extends \ArrayObject
{
    /**
     * @param \SimpleXMLElement $param
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        parent::__construct($this->parseXml($xml->params->param->value->struct->member));
    }

    /**
     * @param array $element
     */
    public function parseXml($members)
    {
        $array = array();

        foreach ($members as $member) {
            $key = (string) $member->name;
            if (isset($member->value->struct)) {
                $array[$key] = $this->parseXml($member->value->struct->member);
            } else {
                foreach ($member->value->children() as $value) {
                    $array[$key] = $this->extractValue($value);
                }
            }
        }

        return $array;
    }

    /**
     * @param \SimpleXMLElement $element
     *
     * @return mixed
     */
    protected function extractValue($element)
    {
        switch ($element->getName()) {
            case 'array':
                $array = array();
                if (isset($element->data->value)) {
                    foreach ($element->data->value as $value) {
                        $array[] = $this->parseXml($value->struct->member);
                    }
                }
                return $array;

            case 'boolean':
                return (bool) $element;

            case 'double':
                return (double) $element;

            case 'int':
                return (int) $element;

            default:
                return (string) $element;
        }
    }
}
