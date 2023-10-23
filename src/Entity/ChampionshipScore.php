<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Enums\GameType;
use App\Repository\ChampionshipScoreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChampionshipScoreRepository::class)]
#[ORM\Table(name: 'championship_scores')]
#[ORM\HasLifecycleCallbacks]
class ChampionshipScore
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['comment' => 'Type of game - group / quarterfinal / semifinal / final'])]
    #[Assert\Choice(callback: [GameType::class, 'values'])]
    private ?int $type = null;

    #[ORM\ManyToOne(inversedBy: 'championshipScores')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $team = null;

    #[ORM\ManyToOne(inversedBy: 'championshipScores')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Championship $championship = null;

    #[ORM\ManyToOne]
    private ?Group $score_group = null;

    #[ORM\Column(options: ['default' => 0, 'comment' => 'Number of points earned by a team in this championship'])]
    private ?int $score = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): static
    {
        $this->team = $team;

        return $this;
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

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getScoreGroup(): ?Group
    {
        return $this->score_group;
    }

    public function setScoreGroup(?Group $score_group): static
    {
        $this->score_group = $score_group;

        return $this;
    }
}
