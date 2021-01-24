<?php
require BASE_DIR . "/app/Web/Routes/routes.php";

use App\Config\ReedOnEnv;
use FastRoute\Dispatcher;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$reedDev = new ReedOnEnv(BASE_DIR . '/.env');
$reedDev->load();

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$logger = new Logger('logger');
$logger->pushHandler(new StreamHandler(dirname(__DIR__) . '/Logs/app/log.log', Logger::DEBUG));
$logger->pushHandler(new FirePHPHandler());

require BASE_DIR . '/app/Web/Routes/routes.php';
/** @var FastRoute\Dispatcher\GroupCountBased $dispatcher */
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        require BASE_DIR . '/app/Web/views/error/error404.html';
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        require BASE_DIR . '/app/Web/views/error/error405.html';
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        if (method_exists($handler[0],call_user_func_array(array(new $handler[0], $handler[1]), $routeInfo[2]))) {
            $response = call_user_func_array(array(new $handler[0], $handler[1]), $routeInfo[2]);
        } else {
            $logger->error( 'Caught exception in file '.__FILE__);
            require BASE_DIR . '/app/Web/views/error/error.html';
            exit;
        }
        break;
    default:
        throw new \Exception('Unexpected value');
}


