<?php

namespace App\Repository;

use App\Entity\Child;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Oro\ORM\Query\AST\Functions\Numeric\TimestampDiff;

/**
 * @method Child|null find($id, $lockMode = null, $lockVersion = null)
 * @method Child|null findOneBy(array $criteria, array $orderBy = null)
 * @method Child[]    findAll()
 * @method Child[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChildRepository extends ServiceEntityRepository
{
    protected EntityManagerInterface $entityManager;

    /** @codeCoverageIgnore */
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Child::class);
        $this->entityManager = $entityManager;
        $this->entityManager->getConfiguration()->addCustomNumericFunction(
            'timestampdiff',
            TimestampDiff::class
        );
    }

    public function persist(Child $entity): Child
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $entity;
    }

    public function findChildrenCurrentlyCheckedIn(string $search, int $start, int $count): array
    {
        $builder = $this->createQueryBuilder('c');
        return $builder
            ->innerJoin('c.attendances', 'a')
            ->where('a.enteredAt >= CURRENT_DATE()')
            ->andWhere("a.enteredAt < DATE_ADD(CURRENT_DATE(), 1, 'day')")
            ->andWhere($builder->expr()->isNull('a.leftAt'))
            ->andWhere('c.name LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->setFirstResult($start)
            ->setMaxResults($count)
            ->getQuery()
            ->execute();
    }

    public function findChildrenByAttendanceDuration(int $start, int $count, int $duration): array
    {
        $builder = $this->createQueryBuilder('c');
        return $builder
            ->innerJoin('c.attendances', 'a')
            ->where('a.enteredAt >= CURRENT_DATE()')
            ->andWhere("a.enteredAt < DATE_ADD(CURRENT_DATE(), 1, 'day')")
            ->andWhere('TIMESTAMPDIFF(minute, a.enteredAt , a.leftAt) >= :duration 
            OR (a.leftAt IS NULL AND TIMESTAMPDIFF(minute, a.enteredAt , CURRENT_TIMESTAMP()) >= :duration)')
            ->setParameter('duration', $duration)
            ->setFirstResult($start)
            ->setMaxResults($count)
            ->getQuery()
            ->execute();
    }
}
