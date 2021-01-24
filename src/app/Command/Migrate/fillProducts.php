<?php

namespace App\Command\Migrate;

use App\Web\Model\prepareXMl;
use App\Web\Model\Product;

class fillProducts
{

    public function fillProducts(): bool
    {
        $model = new Product();
        $sql = $this->fillTableProducts();
        echo $sql;
        $model->query($sql, false, false);

        return true;
    }

    private function fillTableProducts(): bool
    {
        //$path = getenv('path') . "/Storage/products_1927a13ce63d227pl .xml";
        $path = getenv('path') . "/Storage/start.xml";
        $nameSpace = "http://www.w3.org/2001/XMLSchema-instance";
        $xmlData = new prepareXMl($path, $nameSpace);
        $data = $xmlData->loadData();
        $this->fillTableFieldWithXml($data);

        return true;
    }

    private function fillTableFieldWithXml(array $data)
    {
        dump($data);
    }
}