<?php

namespace App\Services;

use App\Entity\Championship;
use App\Repository\ChampionshipRepository;
use Faker\Factory;
use Faker\Generator;

class ChampionshipService
{
    private Generator $faker;

    public function __construct(
        private readonly ChampionshipRepository $championshipRepository,
    )
    {
        $this->faker = Factory::create();
    }

    public function findAll(): array
    {
        return $this->championshipRepository->findAll();
    }

    public function makeChampionship(): Championship
    {
        $championship = new Championship;
        $championship->setName($this->faker->company());

        $this->championshipRepository->save($championship);
        return $championship;
    }
}