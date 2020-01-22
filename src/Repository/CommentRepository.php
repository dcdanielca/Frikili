<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findCommentsDashboard($id)
    {
        $query = $this->getEntityManager()
                ->createQuery(
                    'SELECT c.body, c.publishDate, p.id, p.title
                    FROM App:Comment c
                    JOIN c.post p
                    WHERE c.user = :user
                    ORDER BY c.publishDate DESC
                    ');
        $query->setParameter('user', $id);
        return $query->getResult();
    }

    public function findCommentsPost($id)
    {
        $query = $this->getEntityManager()
                ->createQuery(
                    'SELECT c.body, c.publishDate, u.name
                    FROM App:Comment c
                    JOIN c.user u
                    WHERE c.post = :post
                    ');
        $query->setParameter('post', $id);
        return $query->getResult();
    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
