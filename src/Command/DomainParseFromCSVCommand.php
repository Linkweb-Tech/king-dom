<?php

namespace App\Command;

use App\Controller\Trait\DateTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:parse-domains-from-file',
    description: 'Parse domains from CSV file',
    aliases: ['app:parse-domains-from-file'],
    hidden: false
)]

class DomainParseFromCSVCommand extends Command
{
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        parent::__construct();
    }

    use DateTrait;

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $rowNo = 1;
        if (($fp = fopen("/Users/nicolas_candelon/Documents/Projects/king-dom/csv/domains.csv", "r")) !== FALSE) {
            while (($row = fgetcsv($fp, 1000, ",")) !== FALSE) {
                $num = count($row);
                $rowNo++;
                for ($c=0; $c < $num; $c++) {
                    echo $row[$c] . "\n";
                }
            }
            fclose($fp);
        }

        return Command::SUCCESS;
    }
}