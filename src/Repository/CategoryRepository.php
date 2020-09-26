<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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

    public function save(Category $category): void
    {
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();
    }
}
