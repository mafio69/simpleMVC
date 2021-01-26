<?php

namespace App\Command\Migrate;

use App\Web\Controller\Add\LastCharacters;
use App\Web\Model\prepareXMl;
use App\Web\Model\Product;

class createTableProducts
{
    use LastCharacters;

    const FILE_XML = 'products_1927a13ce63d227pl .xml';
    const FILE_XML_TEST = 'start.xml';

    public function makeProducts(): bool
    {
        $model = new Product();
        $sql = $this->generateSQLTableProducts();
        $model->query($sql, false, false);

        return true;
    }

    public function generateSQLTableProducts(): string
    {
        $path = getenv('path') . '/storage/' . self::FILE_XML;
        $nameSpace = "http://www.w3.org/2001/XMLSchema-instance";
        $xmlData = new prepareXMl($path, $nameSpace);

        return $this->generateTableFieldByXml($xmlData->loadData());
    }

    private function generateTableFieldByXml(array $data): string
    {
        $field = [];
        $buildQuery = /** @lang text */
            "CREATE TABLE IF NOT EXISTS Products ( id INT UNSIGNED AUTO_INCREMENT KEY, ";
        foreach ($data as $record) {
            foreach ($record as $key => $value) {
                $fieldName = (key($value));
                if (!in_array($fieldName, $field)) {
                    $field [] = $fieldName;
                    $buildQuery .= $fieldName . " ";
                    $type = $value[$fieldName]['addFieldTable'] !== null ? 'TEXT' : $value[$fieldName]['type'];
                    $buildQuery .= $type . ", ";
                }
            }
        }
        unset($field);
        $buildQuery = substr($buildQuery, 0, -2);
        $buildQuery .= ")";

        return $buildQuery;
    }
}