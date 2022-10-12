<?php
declare(strict_types=1);

namespace App\Command;

use App\AuctionStatus;
use App\Entity\Auction;
use App\Entity\Lot;
use Doctrine\ORM\Tools\Console\Command\AbstractEntityManagerCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:seed-database', description: 'Seeds the database with example entities.')]
final class SeedDatabaseCommand extends AbstractEntityManagerCommand
{
    protected function configure(): void
    {
        $this->addOption('em', null, InputOption::VALUE_REQUIRED, 'Name of the entity manager to operate on');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $em = $this->getEntityManager($input);

        // Make sure we haven't already run the seeder
        $countExisting = $em->getRepository(Auction::class)->count([]);

        if ($countExisting > 0) {
            $io->info('The database has already been seeded.');

            return Command::SUCCESS;
        }

        $lot1         = new Lot();
        $lot1->title  = 'First Lot';
        $lot1->number = '1';

        $lot2         = new Lot();
        $lot2->title  = 'Second Lot';
        $lot2->number = '2';

        $auction         = new Auction();
        $auction->name   = 'Testing Event';
        $auction->status = AuctionStatus::ACTIVE;
        $auction->addLot($lot1);
        $auction->addLot($lot2);

        $em->persist($auction);
        $em->flush();

        $io->success('The database has been seeded.');

        return Command::SUCCESS;
    }
}
