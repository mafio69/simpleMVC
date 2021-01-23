<?php
namespace App\Web\Model;

use SimpleXMLElement;

class prepareProduct
{
    /**
     * @var SimpleXMLElement|string
     */
    public $data;

    public function __construct(string $filePath, string $nameSpace)
    {
        $this->data = (simplexml_load_file($filePath, 'SimpleXMLElement', 0, $nameSpace));
        $this->data = $this->data->xpath('item');
    }

    public function loadData():array
    {
        $data = [];

        foreach ($this->data as $key => $value) {
            $data[$key] = $this->getOneElement();
        }

        return $data;
    }

    public function flatData():array
    {
        $data = $this->loadData();
        $returnedData = [];

        foreach ($data as $key => $value)
        {
            $fieldName = (key($value[$key]));
            [$fieldName =>$value[$key][$fieldName][1]];

        }

        return  $returnedData ;
    }

    /**
     * @param object $data
     *
     * @return array|false
     */
    public function getKey(object $data)
    {
        if (isset($data)) {
            return get_object_vars($this->data[0]);
        }
        return false;
    }

    /**
     * @return array|false
     */
    public function getOneElement()
    {
        $result = false;
        $propertyArray = $this->getKey($this->data[0]);

        if (is_array($propertyArray === false)) {
            return false;
        }

        foreach ($propertyArray as $key => $value) {
            $type = $this->getType($value);
            if ($type == 'object') {
                $objectVars = get_object_vars($value);

                foreach ($objectVars as $keyTwo => $valueTwo) {
                    $value = $valueTwo;
                    $type = 'array';
                }
            }
            $result[] = [$key => [$type, $value]];
        }

       return  $result;
    }

    private function getType($value): string
    {
        $type = gettype($value);

        switch ($type) {
            case 'object':
                $type = 'object';
                break;
            default:
                $type = preg_match('/^(\d+[.]?)/', $value) ? 'int' : 'string';
                break;
        }

        return $type;
    }
}