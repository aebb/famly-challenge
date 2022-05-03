<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Child;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * @coversDefaultClass \App\Entity\Child
 */
class ChildTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getName
     * @covers ::__toString
     * @covers ::jsonSerialize
     */
    public function testChild()
    {

        $name = 'dummy';
        $child = new Child($name);

        $id = 50;
        $prop = new ReflectionProperty(Child::class, 'id');
        $prop->setAccessible(true);
        $prop->setValue($child, $id);

        $json = [
            'id' => $id,
            'name' => $name,
        ];
        $asString = sprintf('[id : %s, name: %s]', $id, $name);

        $this->assertEquals($name, $child->getName());

        $this->assertEquals($json, $child->jsonSerialize());
        $this->assertEquals($asString, $child->__toString());
    }
}
