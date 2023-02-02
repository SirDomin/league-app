<?php

namespace App\Command;

use App\ApiManager\LeagueApi;
use App\Provider\GameProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:fetch-games')]
class FetchPastGamesCommand extends Command
{
    public function __construct(
        private readonly LeagueApi $leagueApi,
        private readonly GameProvider $gameProvider,
        private readonly EntityManagerInterface $entityManager,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Nickname of player to fetch data.')
            ->addArgument('start', InputArgument::REQUIRED, 'Start of page')
            ->addArgument('limit', InputArgument::REQUIRED, 'Limit of records to get (max 100)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Username: '.$input->getArgument('username'));
        $output->writeln('limit: '.$input->getArgument('limit'));
        $output->writeln('start: '.$input->getArgument('start'));

        $limit = (int) $input->getArgument('limit');
        $start = (int) $input->getArgument('start');

        if ($limit < 1) {
            $output->writeln('Limit must be greater than 1');

            return Command::FAILURE;
        }

        if ($limit > 100) {
            $output->writeln('Limit must be smaller than 100');

            return Command::FAILURE;
        }

        if ($start < 0) {
            $output->writeln('start must be greater than 1');

            return Command::FAILURE;
        }

        $gameIds = $this->leagueApi->getGamesHistory($input->getArgument('username'), $limit, $start);

        $gamesDone = 0;
        $progressBar = new ProgressBar($output, sizeof($gameIds));
        foreach ($gameIds as $gameId) {
            $game = $this->gameProvider->provideGameByMatchId($gameId);

            if ($game->getId() === null) {
                usleep(1500000);
                $this->entityManager->persist($game);
                $this->entityManager->flush();
            }else {
                $output->writeln(sprintf('%s game already in repo', $game->getInfo()->getGameId()));

            }
            $progressBar->advance();
            $gamesDone++;
        }

        $progressBar->finish();
        $output->writeln('');

        $output->writeln(sprintf('%d games were saved!', $gamesDone));
        $output->writeln(sprintf('next args: %s %s', $start + $limit, $limit));

        return Command::SUCCESS;
    }
}
