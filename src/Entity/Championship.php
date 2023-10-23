<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Repository\ChampionshipRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChampionshipRepository::class)]
#[ORM\Table(name: 'championships')]
#[ORM\HasLifecycleCallbacks]
class Championship
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'championship', targetEntity: Group::class, orphanRemoval: true)]
    private Collection $all_groups;

    #[ORM\OneToMany(mappedBy: 'championship', targetEntity: ChampionshipScore::class, orphanRemoval: true)]
    private Collection $championshipScores;

    #[ORM\OneToMany(mappedBy: 'championship', targetEntity: ChampionshipPosition::class, orphanRemoval: true)]
    private Collection $championshipPositions;

    #[ORM\OneToMany(mappedBy: 'championship', targetEntity: Game::class)]
    private Collection $games;

    public function __construct()
    {
        $this->all_groups = new ArrayCollection();
        $this->championshipScores = new ArrayCollection();
        $this->championshipPositions = new ArrayCollection();
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getAllGroups(): Collection
    {
        return $this->all_groups;
    }

    public function addAllGroup(Group $allGroup): static
    {
        if (!$this->all_groups->contains($allGroup)) {
            $this->all_groups->add($allGroup);
            $allGroup->setChampionship($this);
        }

        return $this;
    }

    public function removeAllGroup(Group $allGroup): static
    {
        if ($this->all_groups->removeElement($allGroup)) {
            // set the owning side to null (unless already changed)
            if ($allGroup->getChampionship() === $this) {
                $allGroup->setChampionship(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ChampionshipScore>
     */
    public function getChampionshipScores(): Collection
    {
        return $this->championshipScores;
    }

    public function addChampionshipScore(ChampionshipScore $championshipScore): static
    {
        if (!$this->championshipScores->contains($championshipScore)) {
            $this->championshipScores->add($championshipScore);
            $championshipScore->setChampionship($this);
        }

        return $this;
    }

    public function removeChampionshipScore(ChampionshipScore $championshipScore): static
    {
        if ($this->championshipScores->removeElement($championshipScore)) {
            // set the owning side to null (unless already changed)
            if ($championshipScore->getChampionship() === $this) {
                $championshipScore->setChampionship(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ChampionshipPosition>
     */
    public function getChampionshipPositions(): Collection
    {
        return $this->championshipPositions;
    }

    public function addChampionshipPosition(ChampionshipPosition $championshipPosition): static
    {
        if (!$this->championshipPositions->contains($championshipPosition)) {
            $this->championshipPositions->add($championshipPosition);
            $championshipPosition->setChampionship($this);
        }

        return $this;
    }

    public function removeChampionshipPosition(ChampionshipPosition $championshipPosition): static
    {
        if ($this->championshipPositions->removeElement($championshipPosition)) {
            // set the owning side to null (unless already changed)
            if ($championshipPosition->getChampionship() === $this) {
                $championshipPosition->setChampionship(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->setChampionship($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getChampionship() === $this) {
                $game->setChampionship(null);
            }
        }

        return $this;
    }
}
