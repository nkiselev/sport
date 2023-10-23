<?php

namespace App\Services;

use App\Entity\Championship;
use App\Entity\Game;
use App\Entity\Team;
use App\Enums\GameType;
use App\Repository\ChampionshipScoreRepository;
use App\Repository\GameRepository;

class ChampionshipScoreService
{
    private const COUNT_BEST_TEAMS_IN_GROUPS = 4;
    private const COUNT_BEST_TEAMS_IN_PLAYOFF = [
        GameType::SEMIFINAL->value => 4,
        GameType::FINAL->value => 2,
    ];

    public function __construct(
        private readonly ChampionshipScoreRepository $championshipScoreRepository,
        private readonly GameRepository $gameRepository,
    )
    {
    }

    /**
     * @param Championship $championship
     * @param int $type
     * @return void
     */
    public function calculateScores(Championship $championship, int $type): void
    {
        $this->removeScores($championship, $type);

        foreach ($this->gameRepository->findInChampionshipByType($championship, $type) as $game) {
            $this->calculateScoresForGame($championship, $game, $game->getTeamA(), $game->getScoreA());
            $this->calculateScoresForGame($championship, $game, $game->getTeamB(), $game->getScoreB());
        }
    }

    public function findBestTeamsInPlayoff(Championship $championship, int $type): array
    {
        $take = self::COUNT_BEST_TEAMS_IN_PLAYOFF[$type];

        return $this->championshipScoreRepository
            ->findBestTeamsInPlayoff($championship, $type - 1, $take);
    }

    public function findBestTeamsInGroups(Championship $championship): array
    {
        return $this->championshipScoreRepository
            ->findBestTeamsInGroups($championship, self::COUNT_BEST_TEAMS_IN_GROUPS);
    }

    /**
     * @param Championship $championship
     * @param Game $game
     * @param Team $team
     * @param int $score
     * @return void
     */
    private function calculateScoresForGame(Championship $championship, Game $game, Team $team, int $score): void
    {
        $championshipScore = $this->championshipScoreRepository->findOrCreate(
            $championship, $team, $game->getType()
        );

        $championshipScore->setScoreGroup($game->getGameGroup());
        $championshipScore->setScore( $championshipScore->getScore() + $score );

        $this->championshipScoreRepository->save($championshipScore);
    }

    private function removeScores(Championship $championship, int $type): void
    {
        $this->championshipScoreRepository->remove($championship, $type);
    }
}