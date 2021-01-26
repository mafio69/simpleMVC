<?php

namespace App\Command\Migrate;

use App\Web\Model\prepareXMl;
use App\Web\Model\Product;
use Exception;
use SimpleXMLElement;

class fillProducts
{
    const FILE_XML = 'products_1927a13ce63d227pl .xml';
    const FILE_XML_TEST = 'start.xml';

    public function fillProducts(): bool
    {
        $model = new Product();
        $sqlArray = $this->fillTableProducts();

        foreach ($sqlArray as $sql) {
            try {
                $model->query($sql, false, false);
            } catch (Exception $e) {
                echo 'Caught exception: ', $e->getMessage(), "\n";
            }
        }

        return true;
    }

    private function fillTableProducts(): array
    {
        $path = getenv('path') . "/storage/" . self::FILE_XML;
        $nameSpace = "http://www.w3.org/2001/XMLSchema-instance";
        $xmlData = new prepareXMl($path, $nameSpace);

        return $this->fillTableFieldWithXml($xmlData->loadData());
    }

    private function fillTableFieldWithXml(array $data): array
    {
        $collections = [];

        foreach ($data as $record) {
            $buildQuery = /** @lang text */
                "INSERT INTO Products (";
            foreach ($record as $key => $value) {
                $fieldName = key($value);
                $buildQuery .= $fieldName . ", ";
            }
            $buildQuery = substr($buildQuery, 0, -2) . ") VALUES (";
            foreach ($record as $index => $item) {
                $text = '';
                $fieldName = key($item);
                $dataValue = $item[$fieldName]['value'];
                if ($item[$fieldName]['addFieldTable'] !== null) {
                    if (is_array($item[$fieldName]['value'])) {
                        foreach ($item[$fieldName]['value'] as $values) {
                            if (!$values instanceof SimpleXMLElement) {
                                $text = is_array($values) ? implode(', ', $values) : $values;
                            } else {
                                $text = implode(', ', get_object_vars($values));
                            }
                        }
                    }

                    $dataValue = $text;
                }

                $buildQuery .= "'" . filter_var(trim($dataValue),FILTER_SANITIZE_SPECIAL_CHARS) . "', ";
            }
            $buildQuery = substr($buildQuery, 0, -2) . ")";

            $collections[] = $buildQuery;
        }

        return $collections;
    }
}