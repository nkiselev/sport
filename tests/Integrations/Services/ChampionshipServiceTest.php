<?php

namespace App\Tests\Integrations\Services;

use App\Repository\ChampionshipRepository;
use App\Services\ChampionshipService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChampionshipServiceTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected ChampionshipService $service;

    /**
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->service = self::getContainer()->get(ChampionshipService::class);
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
    public function testMakeChampionship(): void
    {
        $repository = self::getContainer()->get(ChampionshipRepository::class);
        self::assertEmpty($repository->findAll());

        $championship = $this->service->makeChampionship();
        $this->entityManager->flush();
        $this->entityManager->clear();

        self::assertCount(1, $repository->findAll());
        self::assertEquals($repository->findAll()[0]->getId(), $championship->getId());
        self::assertEquals($repository->findAll()[0]->getName(), $championship->getName());
    }
}
