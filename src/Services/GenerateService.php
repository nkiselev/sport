<?php

namespace App\Services;

use App\Entity\Championship;
use App\Entity\ChampionshipScore;
use App\Entity\Group;
use App\Enums\GameType;
use App\Exceptions\TeamCountNotEqualsException;
use Exception;
use Throwable;

readonly class GenerateService
{
    public function __construct(
        private ChampionshipPositionService $championshipPositionService,
        private ChampionshipService      $championshipService,
        private ChampionshipScoreService $championshipScoreService,
        private GameService              $gameService,
        private GroupService             $groupService,
        private TeamService              $teamService,
    )
    {
    }

    /**
     * @param int $groups
     * @param int $teams
     * @return Championship
     */
    public function championship(int $groups, int $teams): Championship
    {
        $championship = $this->championshipService->makeChampionship();

        for ($groupPosition = 0; $groupPosition < $groups; ++$groupPosition) {
            $name = $this->groupService->makeGroupName($groupPosition);
            $group = $this->groupService->makeGroup($name, $championship);

            for ($teamPosition = 0; $teamPosition < $teams; ++$teamPosition) {
                $team = $this->teamService->makeTeam();
                $group->addTeam($team);
            }

            $this->groupService->save($group);
        }

        return $championship;
    }

    /**
     * @param Championship $championship
     * @return void
     * @throws TeamCountNotEqualsException
     * @throws Throwable
     */
    public function games(Championship $championship): void
    {
        // Генерируем игры в группах
        foreach ($championship->getAllGroups() as $group) {
            $this->generateGroupGames($championship, $group);
        }

        $this->generateQuarterGames($championship);
        $this->generatePlayoffGames($championship, GameType::SEMIFINAL->value);
        $this->generatePlayoffGames($championship, GameType::FINAL->value);

        $this->championshipPositionService->calculatePositions($championship);
    }

    /**
     * @param Championship $championship
     * @param int $type
     * @return void
     * @throws TeamCountNotEqualsException
     * @throws Throwable
     */
    private function generatePlayoffGames(Championship $championship, int $type): void
    {
        // Выбираем команды для плейоффа
        $teamsForPlayoff = $this->championshipScoreService->findBestTeamsInPlayoff($championship, $type);
        if ( ! count($teamsForPlayoff)) {
            throw new TeamCountNotEqualsException('Teams for playoff not found. wtf?');
        }

        for ($index = 0, $count = count($teamsForPlayoff) / 2; $index < $count; ++$index) {
            /** @var ChampionshipScore $scoreA */
            $scoreA = array_shift($teamsForPlayoff);
            /** @var ChampionshipScore $scoreB */
            $scoreB = array_pop($teamsForPlayoff);

            $this->gameService->makeGame(
                $championship,
                null,
                $type,
                $scoreA->getTeam(),
                $scoreB->getTeam()
            );
        }

        // Подсчитываем очки, чтобы определить, кто побеждает в плей-оффе
        $this->championshipScoreService->calculateScores($championship, $type);
    }

    /**
     * @param Championship $championship
     * @return void
     * @throws TeamCountNotEqualsException
     * @throws Throwable
     */
    private function generateQuarterGames(Championship $championship): void
    {
        // Подсчитываем очки, чтобы определить, кто выйдет из группы
        $this->championshipScoreService->calculateScores($championship, GameType::GROUP->value);

        // Выбираем команды для четвертьфинала
        $teamsForQuarterfinalFinal = $this->championshipScoreService->findBestTeamsInGroups($championship);
        $teamsA = array_shift($teamsForQuarterfinalFinal);
        $teamsB = array_shift($teamsForQuarterfinalFinal);

        if (count($teamsA) !== count($teamsB)) {
            throw new TeamCountNotEqualsException('Teams count not equals. wtf?');
        }

        for ($index = 0, $count = count($teamsA); $index < $count; ++$index) {
            /** @var ChampionshipScore $scoreA */
            $scoreA = array_shift($teamsA);
            /** @var ChampionshipScore $scoreB */
            $scoreB = array_pop($teamsB);

            $this->gameService->makeGame(
                $championship,
                null,
                GameType::QUARTERFINAL->value,
                $scoreA->getTeam(),
                $scoreB->getTeam()
            );
        }

        // Подсчитываем очки, чтобы определить, кто побеждает в четверть-финале
        $this->championshipScoreService->calculateScores($championship, GameType::QUARTERFINAL->value);
    }

    /**
     * Играем все-со-всеми по одному разу
     *
     * @param Championship $championship
     * @param Group $group
     * @return void
     * @throws Exception
     */
    private function generateGroupGames(Championship $championship, Group $group): void
    {
        $teams = $group->getTeams();
        $teamsCount = $teams->count();

        for ($indexA = 0; $indexA < $teamsCount - 1; ++$indexA) {
            for ($indexB = $indexA + 1; $indexB < $teamsCount; ++$indexB) {
                $teamA = $teams->get($indexA);
                $teamB = $teams->get($indexB);

                $this->gameService->makeGame($championship, $group, GameType::GROUP->value, $teamA, $teamB);
            }
        }
    }

}