<?php

namespace App\Tests\Features\Controller;

use App\Entity\Championship;
use App\Exceptions\TeamCountNotEqualsException;
use App\Repository\ChampionshipRepository;
use App\Services\GenerateService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Throwable;

class ChampionshipControllerTest extends WebTestCase
{
    protected EntityManagerInterface $entityManager;
    protected KernelBrowser $kernelBrowser;
    protected UrlGeneratorInterface $router;

    /**
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->kernelBrowser = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->router = self::getContainer()->get(UrlGeneratorInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser, $this->entityManager, $this->router);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testEmptyMainPage(): void
    {
        $route = $this->router->generate('app_championship_index');
        $this->kernelBrowser->request('GET', $route);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'All championships');
        self::assertSelectorNotExists('a.list-group-item');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testFilledMainPage(): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 3; ++$i) {
            $championship = new Championship();
            $championship->setName($faker->company());
            $this->entityManager->persist($championship);
            $this->entityManager->flush();
        }

        $route = $this->router->generate('app_championship_index');
        $this->kernelBrowser->request('GET', $route);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'All championships');
        self::assertSelectorCount(3, 'a.list-group-item');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testEmptyChampionshipPage(): void
    {
        $faker = Factory::create();

        $championship = new Championship();
        $championship->setName($faker->company());
        $this->entityManager->persist($championship);
        $this->entityManager->flush();

        $route = $this->router->generate('app_championship_show', [
            'championship' => $championship->getId()
        ]);
        $this->kernelBrowser->request('GET', $route);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('nav', $championship->getName());
        self::assertSelectorCount(1, 'form');
        self::assertSelectorCount(0, 'h2');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testWrongChampionshipPage(): void
    {
        $faker = Factory::create();

        $championship = new Championship();
        $championship->setName($faker->company());
        $this->entityManager->persist($championship);
        $this->entityManager->flush();

        $route = $this->router->generate('app_championship_show', [
            'championship' => $championship->getId() + 1
        ]);
        $this->kernelBrowser->request('GET', $route);

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @return void
     * @throws TeamCountNotEqualsException
     * @throws Throwable
     */
    public function testFilledChampionshipPage(): void
    {
        /** @var GenerateService $service */
        $service = self::getContainer()->get(GenerateService::class);
        $championship = $service->championship(2, 6);
        $this->entityManager->flush();
        $this->entityManager->clear();

        /** @var ChampionshipRepository $repository */
        $repository = self::getContainer()->get(ChampionshipRepository::class);
        $championship = $repository->find( $championship->getId() );

        $service->games($championship);

        $route = $this->router->generate('app_championship_show', [
            'championship' => $championship->getId()
        ]);
        $this->kernelBrowser->request('GET', $route);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('nav', $championship->getName());
        self::assertSelectorCount(0, 'form');
        self::assertSelectorCount(6, 'h2');
//        self::assertSelectorTextContains('h2', 'QUARTERFINAL');
//        self::assertSelectorTextContains('h2', 'SEMIFINAL');
//        self::assertSelectorTextContains('h2', 'FINAL');
//        self::assertSelectorTextContains('h2', 'Positions');
    }
}
