<?php

namespace App\Web\Model;

use SimpleXMLElement;

class prepareXMl
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

    public function loadData(): array
    {
        $data = [];

        foreach ($this->data as $key => $value) {
            $data[$key] = $this->getOneElement();
        }

        return $data;
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

            if ($type == 'addTable') {
                $objectVars = get_object_vars($value);
                foreach ($objectVars as $keyTwo => $valueTwo) {
                    $value = $valueTwo;
                    $type = $key;
                }
                continue;
            }

            $result[] = [$key => [$type, $value]];
        }

        return $result;
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

    private function getType($value): string
    {
        $type = gettype($value);

        switch ($type) {
            case 'object':
                $type = $value->count() === 0 ? 'VARCHAR(125)' : 'addTable';
                break;
            default:
                $type = preg_match('/^(\d+[.]?)/', $value) ? 'int' : 'VARCHAR(125)';
                if ($type === 'int') {
                    $type = strpos($value, ".") ? 'FLOAT' : "INT";
                }
                break;
        }

        return $type;
    }
}