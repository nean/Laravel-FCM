<?php

use LaravelFCM\Response\GroupResponse;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;

/**
 * Class GroupResponseTest
 * @uses \LaravelFCM\FCMServiceProvider
 */
class GroupResponseTest extends FCMTestCase
{
    /**
     * @test
     * @covers \LaravelFCM\Response\GroupResponse<extended>
     */
    public function it_logs_when_log_enabled()
    {
        $this->afterApplicationCreated(function () {
            config(['fcm.log_enabled' => true]);
        });

        $notificationKey = 'notificationKey';

        $response = new Response(200, [], '{
					"success": 2,
					"failure": 0
					}');

        Log::shouldReceive('info')
            ->once();

        $groupResponse = new GroupResponse($response, $notificationKey);
    }

    /**
     * @test
     * @covers \LaravelFCM\Response\GroupResponse<extended>
     */
    public function it_does_not_log_when_log_disabled()
    {
        $notificationKey = 'notificationKey';

        $response = new Response(200, [], '{
					"success": 2,
					"failure": 0
					}');

        Log::shouldReceive('info')
            ->never();

        $groupResponse = new GroupResponse($response, $notificationKey);
    }

    /**
     * @test
     * @covers \LaravelFCM\FCMServiceProvider
     * @covers \LaravelFCM\Response\GroupResponse<extended>
     */
    public function it_construct_a_response_with_successes()
    {
        $notificationKey = 'notificationKey';

        $response = new \GuzzleHttp\Psr7\Response(200, [], '{
					"success": 2,
					"failure": 0
					}');

        $responseGroup = new GroupResponse($response, $notificationKey);

        $this->assertEquals(2, $responseGroup->numberSuccess());
        $this->assertEquals(0, $responseGroup->numberFailure());
        $this->assertCount(0, $responseGroup->tokensFailed());
    }

    /**
     * @test
     * @covers \LaravelFCM\FCMServiceProvider
     * @covers \LaravelFCM\Response\GroupResponse<extended>
     */
    public function it_construct_a_response_with_failures()
    {
        $notificationKey = 'notificationKey';

        $response = new \GuzzleHttp\Psr7\Response(200, [], '{
					"success": 0,
					"failure": 2,
					"failed_registration_ids":[
					   "regId1",
					   "regId2"
					]}');

        $responseGroup = new GroupResponse($response, $notificationKey);

        $this->assertEquals(0, $responseGroup->numberSuccess());
        $this->assertEquals(2, $responseGroup->numberFailure());
        $this->assertCount(2, $responseGroup->tokensFailed());

        $this->assertEquals('regId1', $responseGroup->tokensFailed()[0]);
        $this->assertEquals('regId2', $responseGroup->tokensFailed()[1]);
    }

    /**
     * @test
     * @covers \LaravelFCM\FCMServiceProvider
     * @covers \LaravelFCM\Response\GroupResponse<extended>
     */
    public function it_construct_a_response_with_partials_failures()
    {
        $notificationKey = 'notificationKey';

        $response = new \GuzzleHttp\Psr7\Response(200, [], '{
					"success": 1,
					"failure": 2,
					"failed_registration_ids":[
					   "regId1",
					   "regId2"
					]}');

        $responseGroup = new GroupResponse($response, $notificationKey);

        $this->assertEquals(1, $responseGroup->numberSuccess());
        $this->assertEquals(2, $responseGroup->numberFailure());
        $this->assertCount(2, $responseGroup->tokensFailed());
    }
}
