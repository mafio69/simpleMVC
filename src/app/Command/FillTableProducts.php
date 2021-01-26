<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FillTableProducts extends Command
{

    /**
     * @var string
     */
    protected static $defaultName = 'app:fillTableProducts';

    protected function config()
    {
        $this
            ->setDescription('Make migrations')
            ->setHelp('This command allows you to create migrate...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $migrateProducts = (new Migrate\fillProducts())->fillProducts();

        if ($migrateProducts) {
            echo "SUCCESS";
            return Command::SUCCESS;
        } else {
            echo "FAILURE";
            return Command::FAILURE;
        }
    }
}