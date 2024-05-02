<?php

namespace App\Repository;

use App\Entity\Title;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Title>
 *
 * @method Title|null find($id, $lockMode = null, $lockVersion = null)
 * @method Title|null findOneBy(array $criteria, array $orderBy = null)
 * @method Title[]    findAll()
 * @method Title[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TitleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Title::class);
    }

    /**
     * Get all titles.
     *
     * @return Title[]
     */
    public function findAllTitles(): array
    {
        return $this->findAll();
    }

    /**
     * Get a specific title and its associated posts.
     *
     * @param int $titleId The ID of the title
     *
     * @return Title|null
     */
    public function findTitleWithPosts(int $titleId): ?Title
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.posts', 'p')
            ->addSelect('p')
            ->andWhere('t.id = :titleId')
            ->setParameter('titleId', $titleId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
