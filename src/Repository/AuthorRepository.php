<?php

// License proprietary

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * class AuthorRepository.
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    /**
     * AuthorRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    /**
     * @param Author $author
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function save(Author $author)
    {
        $this->_em->persist($author);
    }

    /**
     * @param string $completeName
     *
     * @return Author|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByCompleteName(string $completeName)
    {
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder
            ->where(
                $queryBuilder->expr()->eq(
                    $queryBuilder->expr()->concat(
                        'a.firstName',
                        $queryBuilder->expr()->concat(
                            $queryBuilder->expr()->literal(' '),
                            'a.name'
                        )
                    ),
                    ':completeName'
                )
            )
            ->setParameter('completeName', $completeName);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
