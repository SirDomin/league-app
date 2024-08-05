<?php

namespace App\Utils;

use App\ApiManager\LeagueApi;
use App\Entity\Challenge;
use App\Entity\Game;
use App\Entity\Info;
use App\Entity\Participant;
use App\Repository\GameRepository;
use App\Transformer\InfoTransformer;
use Doctrine\ORM\EntityManagerInterface;

class GameBackfiller
{
    public function __construct(
        private readonly LeagueApi $leagueApi,
        private readonly GameRepository $gameRepository,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function backfillGame(int $gameId): bool
    {
        /** @var Game $game */
        $game = $this->gameRepository->find($gameId);

        $apiGame = $this->leagueApi->getGameById($game->getMetadata()->getMatchId());

        if ($apiGame == [] || $apiGame['info']['participants'] === [] || !$apiGame['info']['participants'] ) {
            return false;
        }

        $apiInfo = InfoTransformer::getInfo($apiGame['info']);

        /** @var Participant $participant */
        foreach ($game->getInfo()->getParticipants() as $participant) {
            /** @var Participant $apiParticipant */
            foreach ($apiInfo->getParticipants() as $apiParticipant) {
                if ($participant->getPuuid() === $apiParticipant->getPuuid()) {
                    $this->backfillParticipant($participant, $apiParticipant);
                }
            }
        }

        $game->setBackfilled(true);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return true;
    }

    private function backfillParticipant(Participant $participantToUpdate, Participant $participantNew): Participant
    {
        $reflect = new \ReflectionClass($participantNew);
        foreach ($reflect->getProperties() as $property) {
            $property->setAccessible(true);
            $newValue = $property->getValue($participantNew);
            if (gettype($newValue) !== 'object' && gettype($newValue) !== 'array' || $property->getName() === 'challenge') {
                if ($property->getName() === 'challenge') {
                    if($participantToUpdate->getChallenges() !== null) {
                        $this->backfillChallenge($participantToUpdate->getChallenges(), $participantNew->getChallenges());
                    }
                } else {
                    if ($newValue !== null && $newValue !== $property->getValue($participantToUpdate)) {
//                        echo $property->getName() . ": " . $newValue . " - " . $property->getValue($participantToUpdate) . PHP_EOL;
//                        echo "\n";

                        $property->setValue($participantToUpdate, $newValue);
                    }
                }

            }

        }

        return $participantToUpdate;
    }

    private function backfillChallenge(Challenge $challengeToUpdate, Challenge $challengeNew): Challenge
    {
        $reflect = new \ReflectionClass($challengeNew);
        foreach ($reflect->getProperties() as $property) {
            $property->setAccessible(true);
            $newValue = $property->getValue($challengeNew);
            if (gettype($newValue) !== 'object' && gettype($newValue) !== 'array') {
                if ($newValue !== null && $newValue !== $property->getValue($challengeToUpdate)) {
//                    echo "(" . $property->getName() . ": " . $newValue . " - " .$property->getValue($challengeToUpdate) .")" . PHP_EOL;
//                    echo "\n";

                    $property->setValue($challengeToUpdate, $newValue);
                }
            }

        }

        return $challengeToUpdate;
    }
}
