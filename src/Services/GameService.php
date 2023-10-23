<?php

namespace App\Services;

use App\Entity\Championship;
use App\Entity\Game;
use App\Entity\Group;
use App\Entity\Team;
use App\Enums\GameType;
use App\Repository\GameRepository;
use Exception;

class GameService
{
    // Количество раундов для вычисления голов забитых командой
    private const GOAL_TIMES = 7;

    public function __construct(private readonly GameRepository $gameRepository)
    {
    }

    public function save(Game $game): Game
    {
        $this->gameRepository->save($game);
        return $game;
    }

    /**
     * @param Championship $championship
     * @param Group|null $group
     * @param int $type
     * @param Team $teamA
     * @param Team $teamB
     * @return Game
     * @throws Exception
     */
    public function makeGame(Championship $championship, ?Group $group, int $type, Team $teamA, Team $teamB): Game
    {
        $goalsA = self::generateGoals($teamA);
        $goalsB = self::generateGoals($teamB);

        // Небольшой "хак" (говнокод) если мы в плейоффе, то будем гарантировать что одна команда точно выиграет
        if ($type !== GameType::GROUP->value && $goalsA === $goalsB) {
            $goalsA = $goalsB + 1;
        }

        $game = new Game;
        $game->setChampionship($championship);
        $game->setGameGroup($group);
        $game->setTeamA($teamA);
        $game->setTeamB($teamB);
        $game->setGoalsA($goalsA);
        $game->setGoalsB($goalsB);
        $game->setScoreA(self::calculateScore($goalsA, $goalsB));
        $game->setScoreB(self::calculateScore($goalsB, $goalsA));
        $game->setType($type);

        return $this->save($game);
    }

    /**
     * Подсчет количества очков за игру.
     * Победа - 3 очка
     * Ничья - 1 очко
     * Проигрыш - 0 очков
     *
     * @param int $goalA
     * @param int $goalB
     * @return int
     */
    private static function calculateScore(int $goalA, int $goalB): int
    {
        return match ($goalA <=> $goalB) {
            0 => 1,
            -1 => 0,
            1 => 3,
        };
    }

    /**
     * @param Team $team
     * @return int
     * @throws Exception
     */
    private static function generateGoals(Team $team): int
    {
        $goals = 0;

        for ($time = 0; $time < self::GOAL_TIMES; ++$time) {
            if (random_int(0, 100) < $team->getStrength()) {
                $goals++;
            }
        }

        return $goals;
    }
}