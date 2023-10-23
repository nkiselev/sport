<?php

namespace App\Controller;

use App\Entity\Championship;
use App\Entity\ChampionshipPosition;
use App\Services\ChampionshipService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app_championship_')]
class ChampionshipController extends AbstractController
{
    #[Route(
        path: '/',
        name: 'index',
        methods: ['GET']
    )]
    public function index(ChampionshipService $championshipService): Response
    {
        return $this->render('index.html.twig', [
            'championships' => $championshipService->findAll()
        ]);
    }

    #[Route(
        path: '/{championship}',
        name: 'show',
        methods: ['GET']
    )]
    public function show(Championship $championship): Response
    {
        $positions = $championship->getChampionshipPositions()->toArray();
        usort($positions, static fn(ChampionshipPosition $positionA, ChampionshipPosition $positionB) =>
            $positionA->getPosition() <=> $positionB->getPosition());

        $games = [];
        foreach ($championship->getGames() as $game) {
            if ($game->getGameGroup()) {
                $games['group'][$game->getGameGroup()->getId()][$game->getTeamA()?->getId()][$game->getTeamB()?->getId()] = $game;
            } else {
                $games['playoff'][$game->getType()][] = $game;
            }
        }

        $scores = [];
        foreach ($championship->getChampionshipScores() as $score) {
            $scores[$score->getType()][$score->getTeam()?->getId()] = $score->getScore();
        }

//        dd($games['playoff']);

        return $this->render('championship.html.twig', [
            'championship' => $championship,
            'groups' => $championship->getAllGroups(),
            'scores' => $scores,
            'positions' => $positions,
            'games' => $games,
        ]);
    }
}