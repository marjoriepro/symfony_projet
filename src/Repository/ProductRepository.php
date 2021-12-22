<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }


    public function searchByTerm($term)
    {
        // QueryBuilder permet de créer des requêtes SQL en PHP
        $queryBuilder = $this->createQueryBuilder('product');

        // Requête
        $query = $queryBuilder
            ->select('product')
            ->leftJoin('product.category', 'category') // leftJoin sur la table type
            ->leftJoin('product.licence', 'licence') // leftJoin sur la table brand
            ->where('product.name LIKE :term') // WHERE en SQL
            ->orWhere('category.name LIKE :term')
            ->orWhere('category.description LIKE :term')
            ->orWhere('licence.name LIKE :term')
            ->orWhere('licence.description LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            // On attribue le term rentré et on sécurise
            ->getQuery();

        return $query->getResult();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
