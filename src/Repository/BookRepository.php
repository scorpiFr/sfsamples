<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use App\Repository\RepositoryAdditionalMethods;

class BookRepository extends ServiceEntityRepository
{
    use RepositoryAdditionalMethods;

    private $idKey = 'id';
    private $baseName = 'testsf';
    private $tableName = 'book';
    private $entityName = 'App\Entity\Book';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, $this->entityName);
    }

}
