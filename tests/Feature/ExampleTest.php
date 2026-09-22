<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_renders_public_school_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSeeText('Tumbuh dalam iman, ilmu, dan adab.')
            ->assertSee('Portal Sekolah');
    }
}
