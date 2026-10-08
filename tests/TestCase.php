<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (file_exists(__DIR__ . '/../.env.testing')) {
            \Dotenv\Dotenv::createImmutable(__DIR__ . '/..', '.env.testing')->load();
        }
    }
}
