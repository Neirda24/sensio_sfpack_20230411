<?php

namespace App\Repository;

use App\Entity\Movie;
use App\EventSubscriber\MovieAddedEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @extends ServiceEntityRepository<Movie>
 *
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry                           $registry,
        private readonly SluggerInterface         $slugger,
        private readonly EventDispatcherInterface $eventDispatcher,
    )
    {
        parent::__construct($registry, Movie::class);
    }

    public function save(Movie $entity, bool $flush = false): void
    {
        $entity->setSlug($entity->getSlug() ?? $this->slugger->slug($entity->getSluggable()));

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
            $this->eventDispatcher->dispatch(new MovieAddedEvent($entity));
        }
    }

    public function remove(Movie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getBySlug(string $slug): Movie
    {
        $qb = $this->createQueryBuilder('movie');

        $qb
            ->andWhere($qb->expr()->eq('movie.slug', ':slug'))
            ->setParameter('slug', $slug);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @return list<Movie>
     */
    public function listAll(): array
    {
        $qb = $this->createQueryBuilder('movie');

        $qb
            ->leftJoin('movie.genres', 'genre')
            ->addSelect('genre');

        return $qb->getQuery()->getResult();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

//    /**
//     * @return Movie[] Returns an array of Movie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Movie
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
