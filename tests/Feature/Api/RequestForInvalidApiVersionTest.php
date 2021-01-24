<?php
declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestForInvalidApiVersionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_will_have_an_error_returned_if_it_requests_an_unsupported_api_version(): void
    {
        $response = $this->getJson('/api/orders', [
            'Accept' => 'application/vnd.quickorder.v3+json',
        ]);

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'type' => 'ApiVersionException',
            'message' => 'Expected one of: "v1", "v2". Got: "v3"',
        ]);
    }
}
