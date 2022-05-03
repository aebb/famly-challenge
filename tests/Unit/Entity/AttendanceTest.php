<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Attendance;
use App\Entity\Child;
use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * @coversDefaultClass \App\Entity\Attendance
 */
class AttendanceTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getLeftAt
     * @covers ::setEnteredAt
     * @covers ::setLeftAt
     * @covers ::jsonSerialize
     */
    public function testAttendance()
    {

        $name = 'dummy';
        $child = new Child($name);

        $attendance = new Attendance($child);
        $date = new DateTime('2022-01-01 00:00:00');

        $attendance->setLeftAt($date);
        $attendance->setEnteredAt($date);

        $id = 50;
        $prop = new ReflectionProperty(Attendance::class, 'id');
        $prop->setAccessible(true);
        $prop->setValue($attendance, $id);

        $prop = new ReflectionProperty(Child::class, 'id');
        $prop->setAccessible(true);
        $prop->setValue($child, $id);

        $json = [
            'id'       => $id,
            'child'    => $child->getName(),
            'checkIn'  => $date->format('Y-m-d H:i:s'),
            'checkOut' => $date->format('Y-m-d H:i:s')
        ];

        $this->assertEquals($json, $attendance->jsonSerialize());
        $this->assertEquals($date, $attendance->getLeftAt());
    }
}
