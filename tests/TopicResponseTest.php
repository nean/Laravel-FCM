<?php

use GuzzleHttp\Psr7\Response;
use LaravelFCM\Response\TopicResponse;
use Illuminate\Support\Facades\Log;
use LaravelFCM\Message\Topics;

/**
 * Class TopicsResponseTest.
 * @uses \LaravelFCM\FCMServiceProvider
 * @uses \LaravelFCM\Message\Topics
 */
class TopicsResponseTest extends FCMTestCase
{
    /**
     * @test
     * @covers \LaravelFCM\Response\TopicResponse<extended>
     */
    public function it_logs_when_log_enabled()
    {
        $this->afterApplicationCreated(function () {
            config(['fcm.log_enabled' => true]);
        });

        $topic = new Topics();
        $topic->topic('topicName');

        $successResponse = new Response(200, [], '{ 
				"message_id": "1234"
		}');

        $errorResponse = new Response(200, [], '{ 
				"error": "MessageTooBig"
		}');

        Log::shouldReceive('info')
            ->times(2);

        $successTopicResponse = new TopicResponse($successResponse, $topic);
        $errorTopicResponse = new TopicResponse($errorResponse, $topic);
    }

    /**
     * @test
     * @covers \LaravelFCM\Response\TopicResponse<extended>
     */
    public function it_does_not_log_when_log_disabled()
    {
        $topic = new Topics();
        $topic->topic('topicName');

        $response = new Response(200, [], '{ 
				"message_id": "1234"
		}');

        Log::shouldReceive('info')
            ->never();

        $topicResponse = new TopicResponse($response, $topic);
    }

    /**
     * @test
     * @covers \LaravelFCM\Response\TopicResponse<extended>
     */
    public function it_construct_a_topic_response_with_success()
    {
        $topic = new Topics();
        $topic->topic('topicName');

        $response = new Response(200, [], '{ 
				"message_id": "1234"
		}');

        $topicResponse = new TopicResponse($response, $topic);

        $this->assertTrue($topicResponse->isSuccess());
        $this->assertFalse($topicResponse->shouldRetry());
        $this->assertNull($topicResponse->error());
    }

    /**
     * @test
     * @covers \LaravelFCM\Response\TopicResponse<extended>
     */
    public function it_construct_a_topic_response_with_error()
    {
        $topic = new \LaravelFCM\Message\Topics();
        $topic->topic('topicName');

        $response = new Response(200, [], '{ 
				"error": "MessageTooBig"
		}');

        $topicResponse = new TopicResponse($response, $topic);

        $this->assertFalse($topicResponse->isSuccess());
        $this->assertFalse($topicResponse->shouldRetry());
        $this->assertEquals('MessageTooBig', $topicResponse->error());
    }

    /**
     * @test
     * @covers \LaravelFCM\Response\TopicResponse<extended>
     */
    public function it_construct_a_topic_response_with_error_and_it_should_retry()
    {
        $topic = new \LaravelFCM\Message\Topics();
        $topic->topic('topicName');

        $response = new Response(200, [], '{ 
				"error": "TopicsMessageRateExceeded"
		}');

        $topicResponse = new TopicResponse($response, $topic);

        $this->assertFalse($topicResponse->isSuccess());
        $this->assertTrue($topicResponse->shouldRetry());
        $this->assertEquals('TopicsMessageRateExceeded', $topicResponse->error());
    }
}
