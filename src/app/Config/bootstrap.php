<?php
require getenv('path') . "/app/Web/Routes/routes.php";

use App\Config\ReedOnEnv;
use FastRoute\Dispatcher;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$reedDev = new ReedOnEnv(getenv('path') . '/.env');
$reedDev->load();

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$logger = new Logger('logger');
$logger->pushHandler(new StreamHandler(dirname(__DIR__) . '/Logs/app/log.log', Logger::DEBUG));
$logger->pushHandler(new FirePHPHandler());
$container = new DI\Container();
require getenv('path') . '/app/Web/Routes/routes.php';
/** @var FastRoute\Dispatcher\GroupCountBased $dispatcher */
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        require getenv('path') . '/app/Web/views/error/error404.html';
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        require getenv('path') . '/app/Web/views/error/error405.html';
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $handler = $routeInfo[1]; //class and method
        $vars = $routeInfo[2]; // variable
        try {
            $container->call($handler, $vars);
        } catch (Exception $e) {
            if (getenv('APP_ENV') === 'dev') {
                echo $e->getMessage();
            } else {
                echo "Sorry, app fail";
            }
            fileLog(date("Y-m-d H:i:m")." ". $e->getMessage());
        }
        break;
}


