<?php

namespace App\Services;

use App\Entity\Championship;
use App\Entity\ChampionshipPosition;
use App\Entity\ChampionshipScore;
use App\Enums\GameType;
use App\Repository\ChampionshipPositionRepository;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\Collection;

readonly class ChampionshipPositionService
{
    public function __construct(
        private ChampionshipPositionRepository $championshipPositionRepository,
        private TeamRepository $teamRepository,
    )
    {
    }

    public function calculatePositions(Championship $championship): array
    {
        $scores = $championship->getChampionshipScores();
        $positions = [];

        $this->fillPositions($positions, $scores, GameType::FINAL->value);
        $this->fillPositions($positions, $scores, GameType::SEMIFINAL->value);
        $this->fillPositions($positions, $scores, GameType::QUARTERFINAL->value);
        $this->fillPositions($positions, $scores, GameType::GROUP->value);

        $this->savePositions($championship, $positions);
        return $positions;
    }

    private function savePositions(Championship $championship, array $positions): void
    {
        for ($index = 1, $count = count($positions); $index <= $count; ++$index) {
            $team = $this->teamRepository->find($positions[$index - 1]);

            $championshipPosition = new ChampionshipPosition;
            $championshipPosition->setChampionship($championship);
            $championshipPosition->setTeam($team);
            $championshipPosition->setPosition($index);

            $this->championshipPositionRepository->save($championshipPosition);
        }
    }

    private function fillPositions(array &$positions, Collection $scores, int $type): void
    {
        $_temp = $this->calculatePositionsByType($scores, $type);
        $_temp = array_diff($_temp, $positions);
        $positions = array_merge($positions, $_temp);
    }

    private function calculatePositionsByType(Collection $scores, int $type): array
    {
        $filtered = $scores
            ->filter(static fn(ChampionshipScore $score) => $score->getType() === $type)
            ->toArray();

        uasort($filtered, static fn(ChampionshipScore $scoreA, ChampionshipScore $scoreB) =>
            $scoreB->getScore() <=> $scoreA->getScore());

        return array_map(static fn(ChampionshipScore $score) => $score->getTeam()?->getId(), $filtered);
    }
}