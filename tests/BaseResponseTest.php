<?php

use GuzzleHttp\Psr7\Response;
use LaravelFCM\Response\BaseResponse;

/**
 * Class BaseResponseTest.
 * @uses \LaravelFCM\FCMServiceProvider
 */
class BaseResponseTest extends FCMTestCase
{
    /**
     * @test
     * @covers \LaravelFCM\Response\BaseResponse
     * @covers \LaravelFCM\Response\Exceptions\InvalidRequestException
     */
    public function it_throws_exception_on_status_code_400()
    {
        $this->expectException(\LaravelFCM\Response\Exceptions\InvalidRequestException::class);

        $rawResponse = new Response(400, [], '{}');
        $someResponse = new class($rawResponse) extends BaseResponse {
            protected function parseResponse($responseInJson) {}
            protected function logResponse() {}
        };
    }

    /**
     * @test
     * @covers \LaravelFCM\Response\BaseResponse
     * @covers \LaravelFCM\Response\Exceptions\UnauthorizedRequestException
     */
    public function it_throws_exception_on_status_code_401()
    {
        $this->expectException(\LaravelFCM\Response\Exceptions\UnauthorizedRequestException::class);

        $rawResponse = new Response(401, [], '{}');
        $someResponse = new class($rawResponse) extends BaseResponse {
            protected function parseResponse($responseInJson) {}
            protected function logResponse() {}
        };
    }

    /**
     * @test
     * @covers \LaravelFCM\Response\BaseResponse
     * @covers \LaravelFCM\Response\Exceptions\ServerResponseException
     */
    public function it_throws_exception_on_status_code_500()
    {
        $this->expectException(\LaravelFCM\Response\Exceptions\ServerResponseException::class);

        $rawResponse = new Response(502, [], '{}');

        $someResponse = new class($rawResponse) extends BaseResponse {
            protected function parseResponse($responseInJson) {}
            protected function logResponse() {}
        };
    }

    /**
     * @test
     * @covers \LaravelFCM\Response\BaseResponse
     * @covers \LaravelFCM\Response\Exceptions\ServerResponseException
     */
    public function it_throws_exception_on_status_code_502()
    {
        $rawResponse = new Response(502, [
            'Retry-After' => '100'
        ], '{}');

        try {
            $someResponse = new class($rawResponse) extends BaseResponse {
                protected function parseResponse($responseInJson) {}
                protected function logResponse() {}
            };
        } catch (\Exception $exception) {
            $this->assertInstanceOf(\LaravelFCM\Response\Exceptions\ServerResponseException::class, $exception);
            $this->assertSame($exception->retryAfter, 100);
        }
    }
}
