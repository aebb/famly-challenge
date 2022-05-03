<?php

namespace App\Tests\Unit\Request\Attendance;

use App\Request\Attendance\CreateRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \App\Request\Attendance\CreateRequest
 * @covers \App\Request\RequestModel
 */
class CreateRequestTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getId
     * @covers ::getRequest
     * @covers ::getToken
     */
    public function testConstruct()
    {
        $request = $this->createMock(Request::class);
        $body = '{"childId":1}';
        $authorizationToken = 'foo-bar';

        $params = $this->createMock(ParameterBagInterface::class);
        $params
            ->expects($this->once())
            ->method('get')
            ->with('X-AUTH-TOKEN')
            ->willReturn($authorizationToken);
        $request->headers = $params;


        $request->expects($this->once())->method('getContent')->willReturn($body);

        $sut = new CreateRequest($request);

        $this->assertEquals(json_decode($body, true)['childId'], $sut->getId());
        $this->assertEquals($authorizationToken, $sut->getToken());
        $this->assertEquals($request, $sut->getRequest());
    }
}
