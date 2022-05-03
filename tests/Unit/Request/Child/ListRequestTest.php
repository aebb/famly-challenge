<?php

namespace App\Tests\Unit\Request\Child;

use App\Request\Child\ListRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \App\Request\Child\ListRequest
 * @covers \App\Request\RequestModel
 */
class ListRequestTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRequest
     * @covers ::getToken
     * @covers ::getSearch
     * @covers ::getStart
     * @covers ::getCount
     */
    public function testConstructWithNullValues()
    {
        $request = $this->createMock(Request::class);

        $params = $this->createMock(ParameterBagInterface::class);
        $params
            ->expects($this->once())
            ->method('get')
            ->with('X-AUTH-TOKEN')
            ->willReturn('');
        $request->headers = $params;

        $params = $this->createMock(ParameterBagInterface::class);
        $params
            ->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(['search'], ['start'], ['count'])
            ->willReturnOnConsecutiveCalls(null, null, null);
        $request->query = $params;

        $model = new ListRequest($request);

        $this->assertEmpty($model->getToken());
        $this->assertNull($model->getSearch());
        $this->assertNull($model->getStart());
        $this->assertNull($model->getCount());
        $this->assertEquals($request, $model->getRequest());
    }

    /**
     * @covers ::__construct
     * @covers ::getRequest
     * @covers ::getToken
     * @covers ::getSearch
     * @covers ::getStart
     * @covers ::getCount
     */
    public function testConstruct()
    {
        $authorizationToken = '__dummy_token__';
        $search = 'John';
        $start = 123;
        $count = 456;

        $request = $this->createMock(Request::class);

        $params = $this->createMock(ParameterBagInterface::class);
        $params
            ->expects($this->once())
            ->method('get')
            ->with('X-AUTH-TOKEN')
            ->willReturn($authorizationToken);
        $request->headers = $params;

        $params = $this->createMock(ParameterBagInterface::class);
        $params
            ->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(['search'], ['start'], ['count'])
            ->willReturnOnConsecutiveCalls($search, $start, $count);
        $request->query = $params;


        $model = new ListRequest($request);

        $this->assertEquals($authorizationToken, $model->getToken());
        $this->assertEquals($search, $model->getSearch());
        $this->assertEquals($start, $model->getStart());
        $this->assertEquals($count, $model->getCount());
        $this->assertEquals($request, $model->getRequest());
    }
}
