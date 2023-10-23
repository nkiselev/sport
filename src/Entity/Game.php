<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Enums\GameType;
use App\Repository\GameRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\Table(name: 'games')]
#[ORM\HasLifecycleCallbacks]
class Game
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Championship $championship = null;

    #[ORM\ManyToOne]
    private ?Group $game_group = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['comment' => 'Type of game - group / quarterfinal / semifinal / final'])]
    #[Assert\Choice(callback: [GameType::class, 'values'])]
    private ?int $type = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $goals_a = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $goals_b = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\Range(min: 0, max: 3)]
    private ?int $score_a = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\Range(min: 0, max: 3)]
    private ?int $score_b = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $team_a = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $team_b = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChampionship(): ?Championship
    {
        return $this->championship;
    }

    public function setChampionship(?Championship $championship): static
    {
        $this->championship = $championship;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getGoalsA(): ?int
    {
        return $this->goals_a;
    }

    public function setGoalsA(int $goals_a): static
    {
        $this->goals_a = $goals_a;

        return $this;
    }

    public function getGoalsB(): ?int
    {
        return $this->goals_b;
    }

    public function setGoalsB(int $goals_b): static
    {
        $this->goals_b = $goals_b;

        return $this;
    }

    public function getScoreA(): ?int
    {
        return $this->score_a;
    }

    public function setScoreA(int $score_a): static
    {
        $this->score_a = $score_a;

        return $this;
    }

    public function getScoreB(): ?int
    {
        return $this->score_b;
    }

    public function setScoreB(int $score_b): static
    {
        $this->score_b = $score_b;

        return $this;
    }

    public function getGameGroup(): ?Group
    {
        return $this->game_group;
    }

    public function setGameGroup(?Group $game_group): static
    {
        $this->game_group = $game_group;

        return $this;
    }

    public function getTeamA(): ?Team
    {
        return $this->team_a;
    }

    public function setTeamA(?Team $team_a): static
    {
        $this->team_a = $team_a;

        return $this;
    }

    public function getTeamB(): ?Team
    {
        return $this->team_b;
    }

    public function setTeamB(?Team $team_b): static
    {
        $this->team_b = $team_b;

        return $this;
    }
}
