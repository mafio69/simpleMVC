<?php


namespace App\Config;


use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class BaseController
{
    /**
     * @var Logger
     */
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger('logger');
        $this->logger->pushHandler(new StreamHandler(BASE_DIR . '/Logs/DB/dbLog.log', Logger::DEBUG));
        $this->logger->pushHandler(new FirePHPHandler());
        $this->logger->info("App run in mode" .getenv('APP_ENV'));
    }

    protected function view(string $view, array $data = []) :bool
    {
        if(file_exists(BASE_DIR.'/App/Web/views/' . $view . '.html.php')){
            include BASE_DIR.'/App/Web/views/' . $view . '.html.php';
            return true;
        } else {
            $this->logger->alert('No such view : ' .$view);
            include BASE_DIR . "/App/Web/views/error/error404.html";
            return false;
        }
    }
}