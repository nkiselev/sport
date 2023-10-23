<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\Table(name: 'teams')]
#[ORM\HasLifecycleCallbacks]
class Team
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['comment' => 'Сила команды, вероятность с которой она победит, чтобы не было совсем скучно'])]
    private ?int $strength = null;

    #[ORM\ManyToMany(targetEntity: group::class, inversedBy: 'teams')]
    private Collection $my_group;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: ChampionshipScore::class, orphanRemoval: true)]
    private Collection $championshipScores;

    public function __construct()
    {
        $this->my_group = new ArrayCollection();
        $this->championshipScores = new ArrayCollection();
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

    public function getStrength(): ?int
    {
        return $this->strength;
    }

    public function setStrength(int $strength): static
    {
        $this->strength = $strength;

        return $this;
    }

    /**
     * @return Collection<int, group>
     */
    public function getMyGroup(): Collection
    {
        return $this->my_group;
    }

    public function addMyGroup(group $myGroup): static
    {
        if (!$this->my_group->contains($myGroup)) {
            $this->my_group->add($myGroup);
        }

        return $this;
    }

    public function removeMyGroup(group $myGroup): static
    {
        $this->my_group->removeElement($myGroup);

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
            $championshipScore->setTeam($this);
        }

        return $this;
    }

    public function removeChampionshipScore(ChampionshipScore $championshipScore): static
    {
        if ($this->championshipScores->removeElement($championshipScore)) {
            // set the owning side to null (unless already changed)
            if ($championshipScore->getTeam() === $this) {
                $championshipScore->setTeam(null);
            }
        }

        return $this;
    }
}
