<?php

namespace App\Tests\Unit\Controller;

use App\Controller\ChildController;
use App\Service\ChildService;
use App\Utils\AppException;
use App\Utils\RequestValidator;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \App\Controller\ChildController
 */
class ChildControllerTest extends TestCase
{
    private RequestValidator $validator;

    private ChildService $service;

    private Request $request;

    private ChildController $sut;

    public function setUp(): void
    {
        $this->validator = $this->createMock(RequestValidator::class);
        $this->service   = $this->createMock(ChildService::class);
        $this->request   = new Request();
        $this->request->headers->set('X-AUTH-TOKEN', 'foobar');
        $this->sut = $this->getMockBuilder(ChildController::class)
            ->setConstructorArgs([ $this->validator, $this->service])
            ->onlyMethods(['json'])
            ->getMock();
    }

    /**
     * @covers::__construct
     * @covers::executeCheckIn
     */
    public function testCheckInAppException()
    {
        $expected = new AppException('dummy-error', 300);
        $this->validator->method('process')->willThrowException($expected);

        $this->sut
            ->expects($this->once())
            ->method('json')
            ->willReturnCallback(function () use ($expected) {
                return new JsonResponse(['error' => $expected->getMessage()], $expected->getCode());
            });

        $result = $this->sut->executeListCheckedIn($this->request);

        $this->assertEquals(json_encode(['error' => $expected->getMessage()]), $result->getContent());
        $this->assertEquals($expected->getCode(), $result->getStatusCode());
    }

    /**
     * @covers::__construct
     * @covers::executeCheckIn
     */
    public function testCheckInSystemException()
    {
        $exception = new Exception();
        $this->validator->method('process')->willThrowException($exception);

        $result = new AppException();
        $this->sut
            ->expects($this->once())
            ->method('json')
            ->willReturnCallback(function () use ($result) {
                return new JsonResponse($result, $result->getStatusCode());
            });

        $result = $this->sut->executeListCheckedIn($this->request);

        $this->assertEquals(
            json_encode(['errorCode' => 0, 'message' => 'Unexpected error']),
            $result->getContent()
        );
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $result->getStatusCode());
    }
}
