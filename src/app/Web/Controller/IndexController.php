<?php
namespace App\Web\Controller;

use App\Config\BaseController;
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
}