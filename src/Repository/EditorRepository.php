<?php

// License proprietary

namespace App\Repository;

use App\Entity\Editor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class EditorRepository.
 *
 * @method Editor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Editor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Editor[]    findAll()
 * @method Editor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EditorRepository extends ServiceEntityRepository
{
    /**
     * EditorRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Editor::class);
    }

    /**
     * @param Editor $editor
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function save(Editor $editor)
    {
        $this->_em->persist($editor);
    }
}
