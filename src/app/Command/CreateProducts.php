<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateProducts extends Command
{
    /**
     * @var string
     */
    protected static  $defaultName = 'app:createProducts';

    protected function config()
    {
        $this
            ->setDescription('Make migrations')
            ->setHelp('This command allows you to creates migrations from the xml file ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $makeProducts = (new Migrate\createTableProducts())->makeProducts();
        $migrateProducts = (new Migrate\fillProducts())->fillProducts();

        if ($migrateProducts && $makeProducts) {
            echo "SUCCESS";
            return Command::SUCCESS;
        } else {
            echo "FAILURE";
            return Command::FAILURE;
        }
    }
}