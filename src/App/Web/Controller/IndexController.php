<?php
namespace App\Web\Controller;

use App\Config\BaseController;
use App\Web\Model\prepareProduct;
use App\Web\Model\Product;

class IndexController extends BaseController
{
    /**
     * @var Product
     */
    private Product $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Product();
    }

    public function index(int $id = null): bool
    {
        echo "start " . $id;

        return true;
    }

    public function getXml()
    {
        $path = BASE_DIR . "/Storage/products_1927a13ce63d227pl .xml";
        $path = BASE_DIR . "/Storage/start.xml";
        $nameSpace = "http://www.w3.org/2001/XMLSchema-instance";
        $xmlData = new prepareProduct($path, $nameSpace);

        $collection = $xmlData->loadData();
        $data = $this->model->getAll('product');

        /*      foreach ($collection as $valueArray) {
                  foreach ($valueArray as $key => $value) {
                      $fieldName = (key($value));
                      dump($value[$fieldName][1]);
                       = $value[$fieldName][1];
                  }
                  $id = R::store( $product );

              }*/
        return $this->view('index',[$data]);
    }
}