<?php

namespace App\Tests\Integrations\Services;

use App\Entity\Championship;
use App\Entity\Group;
use App\Exceptions\TeamCountNotEqualsException;
use App\Repository\ChampionshipPositionRepository;
use App\Repository\ChampionshipRepository;
use App\Repository\ChampionshipScoreRepository;
use App\Repository\GameRepository;
use App\Repository\GroupRepository;
use App\Repository\TeamRepository;
use App\Services\GenerateService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Throwable;

class GenerateServiceTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected GenerateService $service;

    /**
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->service = self::getContainer()->get(GenerateService::class);
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
    public function testGenerateChampionship(): void
    {
        $championshipRepository = self::getContainer()->get(ChampionshipRepository::class);
        self::assertEmpty($championshipRepository->findAll());

        $groupRepository = self::getContainer()->get(GroupRepository::class);
        self::assertEmpty($groupRepository->findAll());

        $teamRepository = self::getContainer()->get(TeamRepository::class);
        self::assertEmpty($teamRepository->findAll());

        $championship = $this->service->championship(2, 6);
        $this->entityManager->flush();
        $this->entityManager->clear();

        self::assertCount(1, $championshipRepository->findAll());
        self::assertCount(2, $groupRepository->findAll());
        self::assertCount(12, $teamRepository->findAll());

        $championship = $championshipRepository->find( $championship->getId() );
        self::assertCount(2, $championship->getAllGroups());

        /** @var Group $group */
        foreach ($championship->getAllGroups() as $group) {
            self::assertCount(6, $group->getTeams());
        }
    }

    /**
     * @return void
     * @throws TeamCountNotEqualsException
     * @throws Throwable
     */
    public function testGenerateGames(): void
    {
        $gameRepository = self::getContainer()->get(GameRepository::class);
        self::assertEmpty($gameRepository->findAll());

        $scoreRepository = self::getContainer()->get(ChampionshipScoreRepository::class);
        self::assertEmpty($scoreRepository->findAll());

        $positionRepository = self::getContainer()->get(ChampionshipPositionRepository::class);
        self::assertEmpty($positionRepository->findAll());

        $championship = $this->service->championship(2, 6);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $championshipRepository = self::getContainer()->get(ChampionshipRepository::class);
        /** @var Championship $championship */
        $championship = $championshipRepository->find( $championship->getId() );

        $this->service->games($championship);

        $this->entityManager->flush();
        $this->entityManager->clear();

        self::assertCount(37, $gameRepository->findAll());
        self::assertCount(26, $scoreRepository->findAll());
        self::assertCount(12, $positionRepository->findAll());

        self::assertCount(37, $championship->getGames());
        self::assertCount(26, $championship->getChampionshipScores());
        self::assertCount(12, $championship->getChampionshipPositions());
    }
}
