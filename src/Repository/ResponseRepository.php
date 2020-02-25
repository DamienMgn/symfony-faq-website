<?php

namespace App\Repository;

use App\Entity\Response;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Response|null find($id, $lockMode = null, $lockVersion = null)
 * @method Response|null findOneBy(array $criteria, array $orderBy = null)
 * @method Response[]    findAll()
 * @method Response[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Response::class);
    }

    public function findByResponsesDisplay($question): array
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.isDisplay = :dis')
            ->setParameter('dis', '1')
            ->andWhere('r.question = :question')
            ->setParameter('question', $question)
            ->getQuery();

            return $qb->execute();
    }
}
