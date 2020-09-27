<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Category::class);
    }

    public function findOneByName(string $name): ?Category
    {
        try {
            $category = $this->createQueryBuilder('c')
                ->where('c.name = :name')
                ->leftJoin('c.products', 'p')
                ->setParameter('name', $name)
                ->getQuery()
                ->getSingleResult();
            return $category instanceof Category
                ? $category
                : null;
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function findCategoriesByProductId(string $id): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.products', 'p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function save(Category $category): void
    {
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();
    }
}
