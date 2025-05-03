<?php

namespace ISend\SMS\Tests;

use ISend\SMS\ISendServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            ISendServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('isend.api_token', 'test-token');
        $app['config']->set('isend.base_url', 'https://isend.com.ly/api/v3');
        $app['config']->set('isend.default_sender_id', 'TestSender');
    }
}