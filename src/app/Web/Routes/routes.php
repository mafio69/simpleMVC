<?php
/** @var RouteCollector $r */

use App\Web\Controller\IndexController;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $r->get('/', [IndexController::class, 'index']);
    $r->get('/get-xml', [IndexController::class, 'getXml']);
    $r->get('/xml-sql', [IndexController::class, 'xmlToSql']);
});
