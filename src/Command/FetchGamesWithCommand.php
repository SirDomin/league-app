<?php

namespace App\Command;

use App\ApiManager\LeagueApi;
use App\Provider\GameProvider;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:fetch-games-with')]
class FetchGamesWithCommand extends Command
{
    public function __construct(
        private readonly LeagueApi $leagueApi,
        private readonly GameProvider $gameProvider,
        private readonly EntityManagerInterface $entityManager,
        private readonly GameRepository $gameRepository,
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


        $gamesFound = [];
        $gamesChecked = 0;

        $data = $this->leagueApi->getSummonerData('SirDomin');

        $progressBar = new ProgressBar($output, (10 - $start) * 100);

        $targetCallsPerMinute = 60;

        $delay = round(60 / $targetCallsPerMinute * 1000000);

        for($x = $start; $x < 10; $x++) {
            $games = $this->leagueApi->getGamesHistory($input->getArgument('username'), 100, $x * 100);

            foreach ($games as $gameId) {
                $gameInRepo = $this->gameRepository->findByMatchId($gameId);

                if ($gameInRepo === null) {
                    $gameInfo = $this->leagueApi->getGameById($gameId);

                    foreach($gameInfo['metadata']['participants'] as $participantUuid) {
                        if ($participantUuid === $data['puuid']) {
                            $game = $this->gameProvider->provideGameByMatchId($gameId);

                            $gamesFound[] = $gameId;
                            $this->entityManager->persist($game);
                            $this->entityManager->flush();
                        }
                    }
                    usleep($delay);
                }
                $gamesChecked++;
                $progressBar->advance();
            }
        }

        $output->writeln(sprintf('found %s games out of %d', count($gamesFound), $gamesChecked));

        return Command::SUCCESS;
    }
}
