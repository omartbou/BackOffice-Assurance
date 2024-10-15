<?php

namespace App\Repository;

use App\Entity\Voiture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Voiture>
 */
class VoitureRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voiture::class);
        $this->entityManager = $this->getEntityManager(); // Get the Entity Manager

    }
    public function saveVoiture(Voiture $voiture){
        $this->entityManager->persist($voiture);
        $this->entityManager->flush();

    }

    public function deleteVoiture(Voiture $voiture)
    {

        $this->entityManager->remove($voiture); // Use the injected Entity Manager
        $this->entityManager->flush();        // Flush changes to the database
    }
    //    /**
    //     * @return Voiture[] Returns an array of Voiture objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Voiture
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
