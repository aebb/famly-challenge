<?php

namespace App\Tests\Integration\Endpoints\Child;

use App\Tests\Integration\EndpointTester;
use App\Tests\Integration\Fixtures\TestFixture;
use DateTime;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass  \App\Controller\ChildController
 * @covers \App\Service\ChildService
 * @covers \App\Repository\ChildRepository
 */
class ListEndpointTest extends EndpointTester
{
    public function testExecuteNoAuth()
    {
        $this->client->request(
            'GET',
            '/child',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => 'fake-token']
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals(['message' => 'Invalid credentials.'], json_decode($response->getContent(), true));
    }

    public function testExecuteBadRequest()
    {
        $fixtures = new TestFixture();
        $fixtures->addUser($this->passwordHasher);
        $this->loadFixture($fixtures);

        $this->client->request(
            'GET',
            '/child?start=x&count=x',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $fixtures->getRecords()[0]->getApiToken()]
        );

        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $expectedResult = [
            'errorCode' => 0,
            'message' => 'start parameter must be an integer & count parameter must be an integer'
        ];

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals($expectedResult, $responseBody);
    }

    public function testExecuteList()
    {
        $fixtures = $this->loadSamples();
        $user = $fixtures->getRecords()[0];
        $this->loadFixture($fixtures);

        $this->client->request(
            'GET',
            '/child',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $user->getApiToken()],
        );

        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $expectedResult = [
            [
                'id' => 1,
                'name' => 'johnny1'
            ],
            [
                'id' => 2,
                'name' => 'johnny2'
            ],
            [
                'id' => 3,
                'name' => 'johnny3'
            ],
            [
                'id' => 4,
                'name' => 'johnny4'
            ],
            [
                'id' => 5,
                'name' => 'hank'
            ],
        ];

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedResult, $responseBody);
    }

    public function testExecuteListStartAndCount()
    {
        $fixtures = $this->loadSamples();
        $user = $fixtures->getRecords()[0];
        $this->loadFixture($fixtures);

        $this->client->request(
            'GET',
            '/child?start=1&count=2',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $user->getApiToken()],
        );

        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $expectedResult = [
            [
                'id' => 2,
                'name' => 'johnny2'
            ],
            [
                'id' => 3,
                'name' => 'johnny3'
            ],
        ];

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedResult, $responseBody);
    }

    public function testExecuteFilter()
    {
        $fixtures = $this->loadSamples();
        $user = $fixtures->getRecords()[0];
        $this->loadFixture($fixtures);

        $this->client->request(
            'GET',
            '/child?search=jo',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $user->getApiToken()],
        );

        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $expectedResult = [
            [
                'id' => 1,
                'name' => 'johnny1'
            ],
            [
                'id' => 2,
                'name' => 'johnny2'
            ],
            [
                'id' => 3,
                'name' => 'johnny3'
            ],
            [
                'id' => 4,
                'name' => 'johnny4'
            ],
        ];

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedResult, $responseBody);
    }

    private function loadSamples()
    {
        $name1 = 'johnny1';
        $name2 = 'johnny2';
        $name3 = 'johnny3';
        $name4 = 'johnny4';
        $name5 = 'hank';
        $nameYesterday = 'john yesterday';
        $nameGone = 'john left';

        $fixtures = new TestFixture();
        $fixtures->addUser($this->passwordHasher);
        $fixtures->addChild($name1);
        $fixtures->addChild($name2);
        $fixtures->addChild($name3);
        $fixtures->addChild($name4);
        $fixtures->addChild($name5);
        $fixtures->addChild($nameYesterday);
        $fixtures->addChild($nameGone);

        $child1 = $fixtures->getRecords()[1];
        $child2 = $fixtures->getRecords()[2];
        $child3 = $fixtures->getRecords()[3];
        $child4 = $fixtures->getRecords()[4];
        $child5 = $fixtures->getRecords()[5];
        $childYesterday = $fixtures->getRecords()[6];
        $childGone = $fixtures->getRecords()[7];

        $date = new DateTime('today midnight');

        //left today already
        $fixtures->addAttendance($childGone, $date);
        $attendanceGone = $fixtures->getRecords()[8];
        $attendanceGone->setLeftAt(new DateTime('today midnight'));

        //yesterday record
        $fixtures->addAttendance($childYesterday, new DateTime('yesterday midnight'));

        $fixtures->addAttendance($child1, $date);
        $fixtures->addAttendance($child2, $date);
        $fixtures->addAttendance($child3, $date);
        $fixtures->addAttendance($child4, $date);
        $fixtures->addAttendance($child5, $date);

        return $fixtures;
    }
}
