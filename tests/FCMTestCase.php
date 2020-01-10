<?php

use Illuminate\Foundation\Testing\TestCase;

abstract class FCMTestCase extends TestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $app->register(LaravelFCM\FCMServiceProvider::class);

        config($this->getDefaultConfig());

        return $app;
    }

    protected function getDefaultConfig()
    {
        return [
            'fcm' => [
                'log_enabled' => false,
                'http' => [
                    'server_key' => 'some_secret_key',
                    'sender_id' => 'some_sender_id',
                    'server_send_url' => 'http://test.test',
                    'timeout' => 20,
                ],
            ]
        ];
    }
}
