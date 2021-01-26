<?php
namespace App\Config;

use Monolog\Logger;

class BaseController
{
    /**
     * @var Logger
     */
    private Logger $logger;

    public function __construct()
    {
        $this->logger = (new InjectContainer)->injectMonolog();

        $this->logger->info("App run in mode: " . getenv('APP_ENV'));
    }

    /** @noinspection PhpIncludeInspection */
    protected function view(string $view, array $data = []): bool
    {
        if (file_exists(getenv('path') . '/App/Web/views/' . $view . '.html.php')) {
            /** @noinspection PhpIncludeInspection */
            include getenv('path') . '/app/Web/views/' . $view . '.html.php';
            return true;
        } else {
            $this->logger->alert('No such view : ' . $view);
            include getenv('path') . "/app/Web/views/error/error404.html";
            return false;
        }
    }
}