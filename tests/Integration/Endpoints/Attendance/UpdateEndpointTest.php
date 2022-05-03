<?php

namespace App\Tests\Integration\Endpoints\Attendance;

use App\Tests\Integration\EndpointTester;
use App\Tests\Integration\Fixtures\TestFixture;
use DateTime;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass  \App\Controller\AttendanceController
 * @covers \App\Service\AttendanceService
 * @covers \App\Repository\AttendanceRepository
 */
class UpdateEndpointTest extends EndpointTester
{
    public function testExecuteNoAuth()
    {
        $this->client->request(
            'PATCH',
            '/attendance/1',
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
            'PATCH',
            '/attendance/X',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $fixtures->getRecords()[0]->getApiToken()]
        );

        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $expectedResult = [
            'errorCode' => 0,
            'message' => 'id parameter must be an integer'
        ];

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals($expectedResult, $responseBody);
    }

    public function testExecutePATCHAttendanceNotFound()
    {
        $fixtures = new TestFixture();
        $fixtures->addUser($this->passwordHasher);
        $this->loadFixture($fixtures);
        $user = $fixtures->getRecords()[0];

        $this->client->request(
            'PATCH',
            '/attendance/1',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $user->getApiToken()],
        );

        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $expectedResult = [
            'errorCode' => 2,
            'message' => 'attendance not found'
        ];

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedResult, $responseBody);
    }

    public function testExecutePATCHAlreadyCheckedOut()
    {
        $name = 'johnny';
        $fixtures = new TestFixture();
        $fixtures->addUser($this->passwordHasher);
        $fixtures->addChild($name);

        $user = $fixtures->getRecords()[0];
        $child = $fixtures->getRecords()[1];

        $fixtures->addAttendance($child, new DateTime());
        $attendance = $fixtures->getRecords()[2];

        $attendance->setLeftAt(new DateTime());
        $this->loadFixture($fixtures);

        $this->client->request(
            'PATCH',
            '/attendance/1',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $user->getApiToken()],
        );

        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $expectedResult = [
            'errorCode' => 2,
            'message' => 'child already checked out'
        ];

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals($expectedResult, $responseBody);
    }

    public function testExecutePATCHSuccess()
    {
        $name = 'johnny';
        $fixtures = new TestFixture();
        $fixtures->addUser($this->passwordHasher);
        $fixtures->addChild($name);

        $user = $fixtures->getRecords()[0];
        $child = $fixtures->getRecords()[1];

        $fixtures->addAttendance($child, new DateTime());
        $this->loadFixture($fixtures);

        $this->client->request(
            'PATCH',
            '/attendance/1',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $user->getApiToken()],
        );

        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertCount(4, $responseBody);
        $this->assertEquals(1, $responseBody['id']);
        $this->assertEquals($name, $responseBody['child']);
        $this->assertTrue((bool) strtotime($responseBody['checkIn']));
        $this->assertTrue((bool) strtotime($responseBody['checkOut']));
    }
}
