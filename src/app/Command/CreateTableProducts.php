<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateTableProducts extends Command
{
    /**
     * @var string
     */
    protected static  $defaultName = 'app:createTableProducts';

    protected function config()
    {
        $this
            ->setDescription('Make migrations')
            ->setHelp('This command allows you to creates migrations from the xml file ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $migrateProducts = (new Migrate\createTableProducts())->makeProducts();

        if ($migrateProducts) {
            echo "SUCCESS";
            return Command::SUCCESS;
        } else {
            echo "FAILURE";
            return Command::FAILURE;
        }
    }
}