<?php


namespace App\Command\Migrate;


use App\Web\Model\prepareXMl;
use App\Web\Model\Product;

class generateProducts
{
    public function makeProducts(): bool
    {
        $model = new Product();
        $sql = $this->generateSQLTableProducts();
        $model->query($sql, false, false);

        return true;
    }

    public function generateSQLTableProducts(): string
    {
        //$path = getenv('path') . "/Storage/products_1927a13ce63d227pl .xml";
        $path = getenv('path') . "/Storage/start.xml";
        $nameSpace = "http://www.w3.org/2001/XMLSchema-instance";
        $xmlData = new prepareXMl($path, $nameSpace);
        $data = $xmlData->loadData();
        $data = $this->generateTableFieldByXml($data);

        return $data;
    }

    private function generateTableFieldByXml(array $data): string
    {
        $buildQuery = /** @lang text */
            "CREATE TABLE IF NOT EXISTS Products (";

        foreach ($data[0] as $key => $value) {
            $fieldName = (key($value));
            $buildQuery .= $fieldName . " ";
            $buildQuery .= ($value[$fieldName][0]) . ", ";
        }

        $buildQuery = substr($buildQuery, 0, -2);
        $buildQuery .= ")";

        return $buildQuery;
    }
}