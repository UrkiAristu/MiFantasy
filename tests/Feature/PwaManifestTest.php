<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaManifestTest extends TestCase
{
    public function test_manifest_is_available_with_pwa_headers(): void
    {
        $response = $this->get('/manifest.json');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/manifest+json');
    }

    public function test_service_worker_is_available_with_javascript_headers(): void
    {
        $response = $this->get('/sw.js');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/javascript');
    }
}
