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
class CreateEndpointTest extends EndpointTester
{
    public function testExecutePostNoAuth()
    {
        $this->client->request(
            'POST',
            '/attendance',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => 'fake-token']
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals(['message' => 'Invalid credentials.'], json_decode($response->getContent(), true));
    }

    public function testExecutePostBadRequest()
    {
        $fixtures = new TestFixture();
        $fixtures->addUser($this->passwordHasher);
        $this->loadFixture($fixtures);

        $this->client->request(
            'POST',
            '/attendance',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $fixtures->getRecords()[0]->getApiToken()]
        );

        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $expectedResult = [
            'errorCode' => 0,
            'message' => 'childId parameter must be present'
        ];

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals($expectedResult, $responseBody);
    }

    public function testExecutePostChildNotFound()
    {
        $fixtures = new TestFixture();
        $fixtures->addUser($this->passwordHasher);
        $this->loadFixture($fixtures);
        $user = $fixtures->getRecords()[0];

        $this->client->request(
            'POST',
            '/attendance',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $user->getApiToken()],
            json_encode(['childId' => 1])
        );

        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $expectedResult = [
            'errorCode' => 1,
            'message' => 'child not found'
        ];

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedResult, $responseBody);
    }

    public function testExecutePostAlreadyCheckedIn()
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
            'POST',
            '/attendance',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $user->getApiToken()],
            json_encode(['childId' => 1])
        );

        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $expectedResult = [
            'errorCode' => 1,
            'message' => 'child already checked in'
        ];

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals($expectedResult, $responseBody);
    }

    public function testExecutePostSuccess()
    {
        $name = 'johnny';
        $fixtures = new TestFixture();
        $fixtures->addUser($this->passwordHasher);
        $fixtures->addChild($name);
        $this->loadFixture($fixtures);

        $user = $fixtures->getRecords()[0];

        $this->client->request(
            'POST',
            '/attendance',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $user->getApiToken()],
            json_encode(['childId' => 1])
        );

        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertCount(3, $responseBody);
        $this->assertEquals(1, $responseBody['id']);
        $this->assertEquals($name, $responseBody['child']);
        $this->assertTrue((bool) strtotime($responseBody['checkIn']));
    }
}
