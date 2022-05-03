<?php

namespace App\Repository;

use App\Entity\Attendance;
use App\Entity\Child;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Attendance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attendance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attendance[]    findAll()
 * @method Attendance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttendanceRepository extends ServiceEntityRepository
{
    protected EntityManagerInterface $entityManager;

    /** @codeCoverageIgnore */
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Attendance::class);
        $this->entityManager = $entityManager;
    }

    public function persist(Attendance $entity): Attendance
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $entity;
    }

    public function update(Attendance $entity): Attendance
    {
        $this->entityManager->flush();
        return $entity;
    }

    public function findAttendedByChild(Child $child): ?Attendance
    {
        $builder = $this->createQueryBuilder('a');
        return $builder
            ->where('a.enteredAt >= CURRENT_DATE()')
            ->andWhere("a.enteredAt < DATE_ADD(CURRENT_DATE(), 1, 'day')")
            ->andWhere($builder->expr()->isNull('a.leftAt'))
            ->andWhere('a.child = :child')
            ->setParameter('child', $child)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
