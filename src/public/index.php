<?php /** @noinspection PhpIncludeInspection */

include '../vendor/autoload.php';
$path= dirname(__DIR__);

putenv("path=$path");
require getenv('path') . "/app/Config/bootstrap.php";
