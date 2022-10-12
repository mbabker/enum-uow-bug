<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\Auction;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Console\Command\AbstractEntityManagerCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Monolog\Handler\TestHandler;
use Monolog\LogRecord;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:update-auction-name', description: 'Updates the name for the specified auction.')]
final class UpdateAuctionNameCommand extends AbstractEntityManagerCommand
{
    public function __construct(private readonly TestHandler $logHandler, ?EntityManagerProvider $entityManagerProvider = null)
    {
        parent::__construct($entityManagerProvider);
    }

    protected function configure(): void
    {
        $this->addOption('em', null, InputOption::VALUE_REQUIRED, 'Name of the entity manager to operate on');
        $this->addOption('auction-id', null, InputOption::VALUE_REQUIRED, 'The ID of the auction to update');
        $this->addOption('name', null, InputOption::VALUE_REQUIRED, 'The new name for the auction');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getOption('name');
        $auctionId = $input->getOption('auction-id');

        if ($name === null || $auctionId === null) {
            $io->error('A value is required for the auction-id and name options.');

            return Command::FAILURE;
        }

        $em = $this->getEntityManager($input);

        try {
            /** @var Auction $auction */
            $auction = $em->createQuery(
                <<<DQL
                    SELECT auction, lots
                    FROM App\Entity\Auction auction
                    LEFT JOIN auction.lots lots
                    WHERE auction.id = :id
                    DQL,
            )
                ->setParameter('id', $auctionId)
                ->getSingleResult();
        } catch (NoResultException) {
            $io->error('The specified auction ID does not exist, have you seeded the database?');

            return Command::FAILURE;
        }

        $auction->name = $name;

        $em->persist($auction);

        $uow = $em->getUnitOfWork();
        $uow->computeChangeSets();

        var_dump((new \ReflectionClass($uow))->getProperty('entityChangeSets')->getValue($uow));

        $em->flush();

        $io->success('The name has been updated.');

        $io->text('DBAL Log Entries:');
        $io->listing(array_map(static fn (LogRecord $record): string => $record->message, $this->logHandler->getRecords()));

        return Command::SUCCESS;
    }
}
