<?php

namespace App\Tests\Integrations\Services;

use App\Repository\TeamRepository;
use App\Services\TeamService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TeamServiceTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected TeamService $service;

    /**
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->service = self::getContainer()->get(TeamService::class);
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
    public function testMakeTeam(): void
    {
        $repository = self::getContainer()->get(TeamRepository::class);
        self::assertEmpty($repository->findAll());

        $team = $this->service->makeTeam();
        $this->entityManager->flush();
        $this->entityManager->clear();

        self::assertCount(1, $repository->findAll());
        self::assertEquals($repository->findAll()[0]->getId(), $team->getId());
        self::assertEquals($repository->findAll()[0]->getName(), $team->getName());
    }
}
