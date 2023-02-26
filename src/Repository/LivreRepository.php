<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livre>
 *
 * @method Livre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livre[]    findAll()
 * @method Livre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    public function save(Livre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Livre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

	public function estEmpruntable(Livre $livre): bool
	{
		$dateActuelle = new \DateTime();

		// Recherche les emprunts actifs associés au livre
		$qb = $this->createQueryBuilder('e')
			->where('e.livre = :livre')
			->andWhere('e.dateRetour >= :dateActuelle OR e.dateRetour IS NULL')
			->andWhere('e.dateEmprunt <= :dateActuelle')
			->setParameter('livre', $livre)
			->setParameter('dateActuelle', $dateActuelle);

		$result = $qb->getQuery()->getResult();

		// Si aucun emprunt actif n'est trouvé, le livre est disponible
		return empty($result);
	}


	public function findAllLivresWithAuteursAndNationalites(): array
	{
		$queryBuilder = $this->createQueryBuilder('l')
			->leftJoin('l.auteurs', 'a')
			->leftJoin('a.nationalite', 'n')
			->leftJoin('l.emprunts', 'e')
			->addSelect('a', 'n', 'e')
			->orderBy('l.titre');
	
		return $queryBuilder->getQuery()->getResult();
	}
	

//    /**
//     * @return Livre[] Returns an array of Livre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Livre
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
