<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    
    /**
     * Get posts associated with a specific user.
     *
     * @param int $userId The ID of the user
     *
     * @return Post[]
     */
    public function findPostsByUser(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'u')
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get posts associated with a specific title.
     *
     * @param int $titleId The ID of the title
     *
     * @return Post[]
     */
    public function findPostsByTitle(int $titleId): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.title', 't')
            ->andWhere('t.id = :titleId')
            ->setParameter('titleId', $titleId)
            ->getQuery()
            ->getResult();
    }
    
}
