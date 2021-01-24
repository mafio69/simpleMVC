<?php
require BASE_DIR . "/App/Web/Routes/routes.php";

use App\Config\ReedOnEnv;
use FastRoute\Dispatcher;

$reedDev = new ReedOnEnv(BASE_DIR . '/.env');
$reedDev->load();

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

require BASE_DIR . '/App/Web/Routes/routes.php';
/** @var FastRoute\Dispatcher\GroupCountBased $dispatcher */
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        require BASE_DIR . '/App/Web/views/error/error404.html';
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        require BASE_DIR . '/App/Web/views/error/error405.html';
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        call_user_func_array(array(new $handler[0], $handler[1]), $routeInfo[2]);
        break;
}


