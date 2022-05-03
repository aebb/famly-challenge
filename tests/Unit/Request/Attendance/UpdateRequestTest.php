<?php

namespace App\Tests\Unit\Request\Attendance;

use App\Request\Attendance\UpdateRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \App\Request\Attendance\UpdateRequest
 * @covers \App\Request\RequestModel
 */
class UpdateRequestTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getId
     * @covers ::getRequest
     * @covers ::getToken
     */
    public function testConstruct()
    {
        $id = 123;
        $request = $this->createMock(Request::class);
        $authorizationToken = 'foo-bar';

        $params = $this->createMock(ParameterBagInterface::class);
        $params
            ->expects($this->once())
            ->method('get')
            ->with('X-AUTH-TOKEN')
            ->willReturn($authorizationToken);
        $request->headers = $params;

        $params = $this->createMock(ParameterBagInterface::class);
        $params
            ->expects($this->once())
            ->method('get')
            ->with('id')
            ->willReturn($id);
        $request->attributes = $params;

        $sut = new UpdateRequest($request);

        $this->assertEquals($authorizationToken, $sut->getToken());
        $this->assertEquals($request, $sut->getRequest());
        $this->assertEquals($id, $sut->getId());
    }
}
