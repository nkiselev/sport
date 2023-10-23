<?php

namespace App\Tests\Integrations\Services;

use App\Entity\Championship;
use App\Entity\Group;
use App\Entity\Team;
use App\Enums\GameType;
use App\Repository\GameRepository;
use App\Services\GameService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GameServiceTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected GameService $service;

    /**
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->service = self::getContainer()->get(GameService::class);
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
    public function testMakeGame(): void
    {
        $repository = self::getContainer()->get(GameRepository::class);
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

        $this->entityManager->flush();

        $game = $this->service->makeGame($championship, null, GameType::FINAL->value, $teamA, $teamB);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $goalsA = $game->getGoalsA();
        $goalsB = $game->getGoalsB();

        self::assertCount(1, $repository->findAll());

        if ($goalsA > $goalsB) {
            self::assertEquals(3, $game->getScoreA());
            self::assertEquals(0, $game->getScoreB());
        } else {
            self::assertEquals(0, $game->getScoreA());
            self::assertEquals(3, $game->getScoreB());
        }
    }
}
