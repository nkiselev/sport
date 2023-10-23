<?php

namespace App\Controller;

use App\Entity\Championship;
use App\Exceptions\TeamCountNotEqualsException;
use App\Requests\ChampionshipCreateRequest;
use App\Services\GenerateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

#[Route('/sport', name: 'app_generate_')]
class GenerateController extends AbstractController
{
    // Жестко хардкодим количество груп, чтобы не усложнять генерацию плейоффов
    private const GROUPS_COUNT = 2;

    /**
     * @param ChampionshipCreateRequest $request
     * @param GenerateService $generateService
     * @return Response
     */
    #[Route(
        path: '/generate/championship',
        name: 'championship',
        methods: ['POST']
    )]
    public function championship(
        #[MapRequestPayload] ChampionshipCreateRequest $request,
        GenerateService $generateService
    ): Response {
        $this->addFlash(
            'notice',
            'New championship were saved!'
        );

        $generateService->championship(self::GROUPS_COUNT, $request->teams);

        return $this->redirectToRoute('app_championship_index');
    }

    /**
     * @param Championship $championship
     * @param GenerateService $generateService
     * @return Response
     * @throws TeamCountNotEqualsException
     * @throws Throwable
     */
    #[Route(
        path: '/generate/games/{championship}',
        name: 'games',
        methods: ['POST']
    )]
    public function games(Championship $championship, GenerateService $generateService): Response {
        $this->addFlash(
            'notice',
            'Games for championship were saved!'
        );

        $generateService->games($championship);

        return $this->redirectToRoute('app_championship_show', [
            'championship' => $championship->getId()
        ]);
    }

}