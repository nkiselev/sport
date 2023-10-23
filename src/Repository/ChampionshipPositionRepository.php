<?php

namespace App\Repository;

use App\Entity\ChampionshipPosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChampionshipPosition>
 *
 * @method ChampionshipPosition|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChampionshipPosition|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChampionshipPosition[]    findAll()
 * @method ChampionshipPosition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChampionshipPositionRepository extends ServiceEntityRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager, ManagerRegistry $registry)
    {
        parent::__construct($registry, ChampionshipPosition::class);
    }

    public function save(ChampionshipPosition $championshipPosition): void
    {
        $this->entityManager->persist($championshipPosition);
        $this->entityManager->flush();
    }
}
