<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
    * @return Question[] Returns an array of Question objects
    */
    
    public function findAllQuestionJoinTags()
    {
        $qb = $this->createQueryBuilder('q')
        ->addSelect('t')
        ->join('q.tags', 't')
        ->orderBy('q.createdAt', 'DESC')
        ;

        return $qb->getQuery();
    }

    public function findAllQuestions()
    {
        $qb = $this->createQueryBuilder('q')
        ->where('q.isDisplay = :val')
        ->setParameter('val' , '1')
        ->orderBy('q.createdAt', 'DESC')
        ;

        return $qb->getQuery();
    }
    
    public function findByString($value)
    {
        $qb = $this->createQueryBuilder('q')
            ->Where('q.title LIKE :val')
            ->setParameter('val' , '%'.$value.'%')
            ->orderBy('q.createdAt', 'DESC')
        ;

        return $qb->getQuery();
    }

    public function findByStringOnlyValidateQuestion($value)
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.isDisplay = :test')
            ->setParameter('test' , '1')
            ->andWhere('q.title LIKE :val')
            ->setParameter('val' , '%'.$value.'%')
            ->orderBy('q.createdAt', 'DESC')
        ;

        return $qb->getQuery();
    }
}
