<?php

namespace App\Services;

use App\Entity\Team;
use App\Repository\TeamRepository;
use Faker\Factory;
use Faker\Generator;

class TeamService
{
    private Generator $faker;

    private const MIN_STRENGTH = 10;
    private const MAX_STRENGTH = 50;

    public function __construct(private readonly TeamRepository $teamRepository)
    {
        $this->faker = Factory::create();
    }

    public function makeTeam(): Team
    {
        $team = new Team;
        $team->setName($this->faker->company());
        $team->setStrength($this->faker->numberBetween(self::MIN_STRENGTH, self::MAX_STRENGTH));

        $this->teamRepository->save($team);
        return $team;
    }

}