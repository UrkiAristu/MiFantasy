<?php

namespace Tests\Feature;

use Tests\TestCase;

class DatabaseConfigTest extends TestCase
{
    public function test_database_config(): void
    {
        $this->assertEquals('sqlite', config('database.default'));
        $this->assertEquals(':memory:', config('database.connections.sqlite.database'));
    }
}
