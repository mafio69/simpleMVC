<?php
namespace App\Web\Model;

use App\Web\Controller\Add\LastCharacters;
use SimpleXMLElement;

class prepareXMl
{
    use LastCharacters;

    /**
     * @var SimpleXMLElement|string
     */
    public $data;

    public function __construct(string $filePath, string $nameSpace)
    {
        $this->data = new SimpleXMLElement(file_get_contents($filePath), LIBXML_NOCDATA, null
            , $nameSpace);
        $this->data = $this->data->xpath('item');
    }

    public function loadData(): array
    {
        $data = [];
        foreach ($this->data as $key => $value) {
            $data[] = $this->getOneElementToCreate($value);
        }

        return $data;
    }

    /**
     * @param SimpleXMLElement $data
     *
     * @return array
     */
    public function getOneElementToCreate(SimpleXMLElement $data): array
    {
        $propertyArray = $this->getKey($data[0]);

        if (!count($propertyArray)) {
            return [];
        }

        return $this->generateData($propertyArray);
    }

    /**
     * @param array $propertyArray
     *
     * @return array|false
     */
    private function generateData(array $propertyArray)
    {
        $nameField = null;
        $result = [];
        $temp = [];

        if (is_array($propertyArray === false)) {
            return false;
        }

        foreach ($propertyArray as $key => $value) {
            $type = $this->getType($value);

            if (strpos($type, '=')) {
                $nameField = $this->getLastChar('=', $type);
                $objectVars = get_object_vars($value);

                foreach ($objectVars as $keyTwo => $valueTwo) {
                    $temp[] = $valueTwo;
                    $type = $key;
                    $value = $temp;
                }
            } else {
                $nameField = null;
            }

            $result[] = [$key => ['type' => $type, 'value' => $value, 'addFieldTable' => $nameField]];
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
            return get_object_vars($data[0]);
        }
        return false;
    }

    private function getType($value): string
    {
        $type = gettype($value);

        switch ($type) {
            case 'object':
                if (!empty(key($value[0]))) {
                    $keyAddTable = (key($value[0]));
                    $type = "TEXT={$keyAddTable}";
                } else {
                    $type = 'VARCHAR(51)';
                }
                break;
            default:
                $type = preg_match('/^(\d+[.]?\b)/', $value) && strlen($value) < 11 ? 'INT' : 'VARCHAR(255)';
                if ($type === 'INT' && strpos($value, ".")) {
                    $type = 'FLOAT';
                } elseif (strlen($value) > 125) {
                    $type = 'TEXT';
                }

                break;
        }

        return $type;
    }
}