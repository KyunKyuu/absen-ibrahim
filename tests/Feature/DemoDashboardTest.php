<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoDashboardTest extends TestCase
{
    public function test_demo_dashboard_is_public_and_renders_client_features(): void
    {
        $this->get('/demo-dashboard')
            ->assertOk()
            ->assertSee('Demo dashboard untuk presentasi client')
            ->assertSee('Absensi siap untuk GPS dan fingerprint')
            ->assertSee('Preview Raport Sikap');
    }
}
