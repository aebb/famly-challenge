<?php

namespace App\Tests\Unit\Repository;

use App\Entity\Child;
use App\Repository\ChildRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/** @coversDefaultClass \App\Repository\ChildRepository */
class ChildRepositoryTest extends TestCase
{
    protected EntityManagerInterface $entityManager;
    protected ChildRepository $sut;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->sut = $this->getMockBuilder(ChildRepository::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept([
                'persist',
                'findChildrenByAttendanceDuration'
            ])
            ->getMock();

        $prop = new ReflectionProperty(ChildRepository::class, 'entityManager');
        $prop->setAccessible(true);
        $prop->setValue($this->sut, $this->entityManager);
    }

    /**
     * @covers ::persist
     */
    public function testPersist()
    {
        $model = $this->createMock(Child::class);

        $this->entityManager->expects($this->once())->method('persist')->with($model);
        $this->entityManager->expects($this->once())->method('flush');

        $this->assertSame($model, $this->sut->persist($model));
    }

    /**
     * @covers::findChildrenByAttendanceDuration
     */
    public function testFindChildrenByAttendanceDuration()
    {
        $start = 1;
        $count = 1;
        $duration = 1;

        $expected = [$this->createMock(Child::class)];
        $builder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);


        $builder->expects($this->once())
            ->method('innerJoin')
            ->with('c.attendances', 'a')
            ->willReturn($builder);

        $builder->expects($this->once())
            ->method('where')
            ->with('a.enteredAt >= CURRENT_DATE()')
            ->willReturn($builder);

        $builder->expects($this->exactly(2))
            ->method('andWhere')
            ->withConsecutive(
                ["a.enteredAt < DATE_ADD(CURRENT_DATE(), 1, 'day')"],
                ["TIMESTAMPDIFF(minute, a.enteredAt , a.leftAt) >= :duration 
            OR (a.leftAt IS NULL AND TIMESTAMPDIFF(minute, a.enteredAt , CURRENT_TIMESTAMP()) >= :duration)"]
            )
            ->willReturnOnConsecutiveCalls($builder, $builder);

        $builder
            ->expects($this->once())
            ->method('setParameter')
            ->with('duration', $duration)
            ->willReturn($builder);

        $builder
            ->expects($this->once())
            ->method('setFirstResult')
            ->with($start)
            ->willReturn($builder);

        $builder
            ->expects($this->once())
            ->method('setMaxResults')
            ->with($count)
            ->willReturn($builder);

        $builder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $query->expects($this->once())
            ->method('execute')
            ->willReturn($expected);

        $this->sut->expects($this->once())
            ->method('createQueryBuilder')
            ->with('c')
            ->willReturn($builder);

        $this->assertEquals($expected, $this->sut->findChildrenByAttendanceDuration($start, $count, $duration));
    }
}
