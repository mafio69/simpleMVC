<?php

use App\Web\Services\AppExceptions\AppException;

include '../vendor/autoload.php';
define('BASE_DIR', dirname(__DIR__));

if (require BASE_DIR . "/app/Config/bootstrap.php") {

}
try {
    require BASE_DIR . "/app/Config/bootstrap.php";
} catch (AppException $e) {
    require BASE_DIR . '/../src/app/Web/views/error/error.html';
}
