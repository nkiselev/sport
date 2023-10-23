<?php

namespace App\Requests;

use Symfony\Component\Validator\Constraints as Assert;

class ChampionshipCreateRequest
{
    #[Assert\Range(min: 2, max: 4)]
    public int $groups = 2;

    #[Assert\Range(min: 6, max: 10)]
    public int $teams;

}