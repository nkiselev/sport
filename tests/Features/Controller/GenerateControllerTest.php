<?php

namespace App\Tests\Features\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Championship;
use App\Repository\ChampionshipPositionRepository;
use App\Repository\ChampionshipRepository;
use App\Repository\ChampionshipScoreRepository;
use App\Repository\GameRepository;
use App\Repository\GroupRepository;
use App\Repository\TeamRepository;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Throwable;

class GenerateControllerTest extends ApiTestCase
{
    protected Client $client;
    protected UrlGeneratorInterface $router;

    /**
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        /** @var UrlGeneratorInterface $router */
        $this->router = self::getContainer()->get(UrlGeneratorInterface::class);
    }

    /**
     * @return void
     * @throws TransportExceptionInterface
     */
    public function testGenerateAllowedOnlyPOSTMethod(): void
    {
        $route = $this->router->generate('app_generate_championship');

        $this->client->request('GET', $route);
        self::assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->client->request('PUT', $route);
        self::assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->client->request('DELETE', $route);
        self::assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->client->request('PATCH', $route);
        self::assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * @return void
     * @throws TransportExceptionInterface
     * @throws Throwable
     */
    public function testGenerateNewChampionship(): void
    {
        $championshipRepository = self::getContainer()->get(ChampionshipRepository::class);
        $this->assertEmpty($championshipRepository->findAll());

        $groupRepository = self::getContainer()->get(GroupRepository::class);
        $this->assertEmpty($groupRepository->findAll());

        $teamRepository = self::getContainer()->get(TeamRepository::class);
        $this->assertEmpty($teamRepository->findAll());

        $route = $this->router->generate('app_generate_championship');

        $this->client->request('POST', $route, ['json' => [
            'teams' => 6,
        ]]);
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->assertCount(1, $championshipRepository->findAll());
        $this->assertCount(2, $groupRepository->findAll());
        $this->assertCount(12, $teamRepository->findAll());
    }

    /**
     * @return void
     * @throws TransportExceptionInterface
     */
    public function testGenerateNewChampionshipWithWrongParams(): void
    {
        $route = $this->router->generate('app_generate_championship');

        $this->client->request('POST', $route, ['json' => []]);
        self::assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);

        $this->client->request('POST', $route, ['json' => [
            'teams' => 5,
        ]]);
        self::assertResponseIsUnprocessable('This value should be between 6 and 10.');

        $this->client->request('POST', $route, ['json' => [
            'teams' => 11,
        ]]);
        self::assertResponseIsUnprocessable('This value should be between 6 and 10.');
    }

    /**
     * @return void
     * @throws TransportExceptionInterface
     * @throws Throwable
     */
    public function testGenerateGamesForChampionship(): void
    {
        $route = $this->router->generate('app_generate_championship');

        $this->client->request('POST', $route, ['json' => [
            'teams' => 6,
        ]]);

        $championshipRepository = self::getContainer()->get(ChampionshipRepository::class);
        /** @var Championship $championship */
        $championship = $championshipRepository->findAll()[0];

        //-----------------

        $gameRepository = self::getContainer()->get(GameRepository::class);
        $this->assertEmpty($gameRepository->findAll());

        $positionRepository = self::getContainer()->get(ChampionshipPositionRepository::class);
        $this->assertEmpty($positionRepository->findAll());

        $scoreRepository = self::getContainer()->get(ChampionshipScoreRepository::class);
        $this->assertEmpty($scoreRepository->findAll());

        $route = $this->router->generate('app_generate_games', [
            'championship' => $championship->getId()
        ]);
        $this->client->request('POST', $route);
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->assertCount(37, $gameRepository->findAll());
        $this->assertCount(12, $positionRepository->findAll());
        $this->assertCount(26, $scoreRepository->findAll());
    }
}
