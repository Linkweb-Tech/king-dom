<?php
namespace App\Command;

use App\Controller\Domain\SnapController;
use App\Controller\Trait\DateTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:find-domains',
    description: 'Lauch check domain function.',
    aliases: ['app:find-domains'],
    hidden: false
)]
class CheckDomainsCommand extends Command
{
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        parent::__construct();
    }

    use DateTrait;
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $controller = new SnapController($this->manager);
        $result = $controller->launchConnexion();
        $output->writeln($result);
        $time = $this->getTodayFormatted();
        // outputs a message followed by a "\n"
        $output->writeln('Execution : ' . $time->format('d-m-Y H:i'));
        if($result['return']){
            $output->writeln('Tentative faite sur ' . $result['domain']);
        } else {
            $output->writeln('Aucune tentative envoyée sur ' . $result ['domain']);
        }

        return Command::SUCCESS;
    }

}