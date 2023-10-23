<?php

namespace App\Tests\Integrations\Services;

use App\Entity\Championship;
use App\Entity\ChampionshipScore;
use App\Entity\Game;
use App\Entity\Team;
use App\Enums\GameType;
use App\Repository\ChampionshipRepository;
use App\Repository\ChampionshipScoreRepository;
use App\Services\ChampionshipScoreService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChampionshipScoreServiceTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected ChampionshipScoreService $service;

    /**
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->service = self::getContainer()->get(ChampionshipScoreService::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->entityManager, $this->service);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testCalculateScores(): void
    {
        $repository = self::getContainer()->get(ChampionshipScoreRepository::class);
        self::assertEmpty($repository->findAll());

        $championship = new Championship;
        $championship->setName('Championship');
        $this->entityManager->persist($championship);

        $teamA = new Team();
        $teamA->setName('teamA');
        $teamA->setStrength(50);
        $this->entityManager->persist($teamA);

        $teamB = new Team();
        $teamB->setName('teamB');
        $teamB->setStrength(50);
        $this->entityManager->persist($teamB);

        $game = new Game;
        $game->setChampionship($championship);
        $game->setType(GameType::FINAL->value);
        $game->setTeamA($teamA);
        $game->setTeamB($teamB);
        $game->setGoalsA(2);
        $game->setGoalsB(1);
        $game->setScoreA(3);
        $game->setScoreB(0);
        $this->entityManager->persist($game);

        $this->entityManager->flush();
        $this->entityManager->clear();

        $championshipRepository = self::getContainer()->get(ChampionshipRepository::class);
        /** @var Championship $championship */
        $championship = $championshipRepository->find( $championship->getId() );

        $this->service->calculateScores($championship, GameType::FINAL->value);
        $this->entityManager->flush();
        $this->entityManager->clear();

        self::assertCount(2, $repository->findAll());

        /** @var ChampionshipScore $score */
        $score = $repository->findOneBy(['team' => $teamA]);
        self::assertEquals(3, $score->getScore());

        $score = $repository->findOneBy(['team' => $teamB]);
        self::assertEquals(0, $score->getScore());
    }
}
