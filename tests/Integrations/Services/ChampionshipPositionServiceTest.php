<?php

namespace App\Tests\Integrations\Services;

use App\Entity\Championship;
use App\Entity\ChampionshipPosition;
use App\Entity\ChampionshipScore;
use App\Entity\Team;
use App\Enums\GameType;
use App\Repository\ChampionshipPositionRepository;
use App\Repository\ChampionshipRepository;
use App\Services\ChampionshipPositionService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChampionshipPositionServiceTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected ChampionshipPositionService $service;

    /**
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->service = self::getContainer()->get(ChampionshipPositionService::class);
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
    public function testGetBestPosition(): void
    {
        $repository = self::getContainer()->get(ChampionshipPositionRepository::class);
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

        $score1A = new ChampionshipScore();
        $score1A->setChampionship($championship);
        $score1A->setTeam($teamA);
        $score1A->setType(GameType::FINAL->value);
        $score1A->setScore(3);
        $this->entityManager->persist($score1A);

        $score1B = new ChampionshipScore();
        $score1B->setChampionship($championship);
        $score1B->setTeam($teamB);
        $score1B->setType(GameType::FINAL->value);
        $score1B->setScore(1);
        $this->entityManager->persist($score1B);

        $score2A = new ChampionshipScore();
        $score2A->setChampionship($championship);
        $score2A->setTeam($teamA);
        $score2A->setType(GameType::SEMIFINAL->value);
        $score2A->setScore(1);
        $this->entityManager->persist($score2A);

        $score2B = new ChampionshipScore();
        $score2B->setChampionship($championship);
        $score2B->setTeam($teamB);
        $score2B->setType(GameType::SEMIFINAL->value);
        $score2B->setScore(3);
        $this->entityManager->persist($score2B);

        $this->entityManager->flush();
        $this->entityManager->clear();

        $championshipRepository = self::getContainer()->get(ChampionshipRepository::class);
        /** @var Championship $championship */
        $championship = $championshipRepository->find( $championship->getId() );

        $this->service->calculatePositions($championship);
        $this->entityManager->flush();
        $this->entityManager->clear();

        self::assertCount(2, $repository->findAll());

        /** @var ChampionshipPosition $position */
        $position = $repository->findOneBy(['team' => $teamA]);
        self::assertEquals(1, $position->getPosition());

        $position = $repository->findOneBy(['team' => $teamB]);
        self::assertEquals(2, $position->getPosition());
    }
}
