<?php

namespace App\Tests\Unit\Utils;

use App\Utils\AppException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \App\Utils\AppException
 */
class AppExceptionTest extends TestCase
{
    /**
    * @covers ::__construct
    * @covers ::getStatusCode
    * @covers ::jsonSerialize
    */
    public function testAppException()
    {
        $sut = new AppException();
        $expected = ['errorCode' => 0,'message' => 'Unexpected error'];

        $this->assertEquals(500, $sut->getStatusCode());
        $this->assertEquals($expected, $sut->jsonSerialize());
    }
}
