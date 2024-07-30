<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function searchFor($query)
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.categories', 'c')
            ->where('p.title LIKE :query')
            ->orWhere('p.description LIKE :query')
            ->orWhere('c.Name LIKE :query')
            ->groupBy('p.id')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }
}
