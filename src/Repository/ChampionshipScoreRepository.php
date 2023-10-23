<?php

namespace App\Repository;

use App\Entity\Championship;
use App\Entity\ChampionshipScore;
use App\Entity\Team;
use App\Enums\GameType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChampionshipScore>
 *
 * @method ChampionshipScore|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChampionshipScore|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChampionshipScore[]    findAll()
 * @method ChampionshipScore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChampionshipScoreRepository extends ServiceEntityRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager, ManagerRegistry $registry)
    {
        parent::__construct($registry, ChampionshipScore::class);
    }

    /**
     * @param Championship $championship
     * @param Team $team
     * @param int $type
     * @return ChampionshipScore
     */
    public function findOrCreate(Championship $championship, Team $team, int $type): ChampionshipScore
    {
        $championshipScore = $this->findOneBy([
            'championship' => $championship,
            'team' => $team,
            'type' => $type,
        ]);

        if ($championshipScore === null) {
            $championshipScore = new ChampionshipScore;
            $championshipScore->setChampionship($championship);
            $championshipScore->setTeam($team);
            $championshipScore->setType($type);
            $championshipScore->setScore(0);
        }

        return $championshipScore;
    }

    public function findBestTeamsInPlayoff(Championship $championship, int $type, int $take): array
    {
        return $this->findBy([
                'championship' => $championship,
                'type' => $type,
            ], [
                'score' => 'desc'
            ], $take);
    }

    public function findBestTeamsInGroups(Championship $championship, int $take): array
    {
        $result = [];
        foreach ($championship->getAllGroups() as $group) {
            $result[$group->getId()] = $this->findBy([
                'championship' => $championship,
                'score_group' => $group,
                'type' => GameType::GROUP->value,
            ], [
                'score' => 'desc'
            ], $take);
        }

        return $result;
    }

    public function save(ChampionshipScore $championshipScore): void
    {
        $this->entityManager->persist($championshipScore);
        $this->entityManager->flush();
    }

    /**
     * @param Championship $championship
     * @param int $type
     * @return float|int|mixed|string
     */
    public function remove(Championship $championship, int $type): mixed
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.championship = :championship')
            ->andWhere('s.type = :type')
            ->setParameter('championship', $championship)
            ->setParameter('type', $type)
            ->delete()
            ->getQuery()
            ->execute();
    }
}
