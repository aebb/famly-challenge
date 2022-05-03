<?php

namespace App\Tests\Unit\Utils;

use App\Request\Child\ListRequest;
use App\Utils\AppException;
use App\Utils\RequestValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @coversDefaultClass \App\Utils\RequestValidator
 */
class RequestValidatorTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::process
     */
    public function testProcessThrowsException()
    {
        $message = "dummy error";
        $error = $this->createMock(ConstraintViolation::class);
        $error->expects($this->once())->method('getMessage')->willReturn($message);

        $validator = $this->createMock(ValidatorInterface::class);
        $errors = new ConstraintViolationList([$error]);

        $request = $this->createMock(ListRequest::class);

        $validator->expects($this->once())
            ->method('validate')
            ->with($request)
            ->willReturn($errors);

        $this->expectException(AppException::class);

        $sut = new RequestValidator($validator);
        $sut->process($request);
    }

    /**
     * @covers ::__construct
     * @covers ::process
     */
    public function testProcessSuccess()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $errors = $this->createMock(ConstraintViolationListInterface::class);
        $request = $this->createMock(ListRequest::class);

        $validator->expects($this->once())
            ->method('validate')
            ->with($this->createMock(ListRequest::class))
            ->willReturn($errors);

        $errors->expects($this->once())->method('count')->willReturn(0);

        $sut = new RequestValidator($validator);
        $this->assertEquals($request, $sut->process($request));
    }
}
