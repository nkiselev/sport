<?php

namespace App\Tests\Integrations\Services;

use App\Entity\Championship;
use App\Repository\GroupRepository;
use App\Services\GroupService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GroupServiceTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected GroupService $service;

    /**
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->service = self::getContainer()->get(GroupService::class);
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
    public function testMakeGroup(): void
    {
        $repository = self::getContainer()->get(GroupRepository::class);
        self::assertEmpty($repository->findAll());

        $championship = new Championship;
        $championship->setName('Championship');
        $this->entityManager->persist($championship);
        $this->entityManager->flush();

        $group = $this->service->makeGroup('TEST', $championship);
        $this->entityManager->flush();
        $this->entityManager->clear();

        self::assertCount(1, $repository->findAll());
        self::assertEquals($repository->findAll()[0]->getId(), $group->getId());
        self::assertEquals('TEST', $group->getName());
    }

    public function testMakeGroupName(): void
    {
        self::assertEquals('A', $this->service->makeGroupName(0));
        self::assertEquals('B', $this->service->makeGroupName(1));
        self::assertEquals('C', $this->service->makeGroupName(2));
        self::assertEquals('D', $this->service->makeGroupName(3));
    }
}
