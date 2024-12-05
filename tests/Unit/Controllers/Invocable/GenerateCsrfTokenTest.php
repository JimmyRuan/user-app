<?php

namespace Tests\Unit\Controllers\Invocable;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateCsrfTokenTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'http://localhost']);
    }

    public function test_valid_request_returns_csrf_token()
    {
        $this->withSession(['_token' => 'valid-token'])
            ->withHeaders([
                'X-CSRF-TOKEN' => 'valid-token',
                'referer' => 'http://localhost/some-page',
            ])
            ->get('/csrf-token')
            ->assertOk()
            ->assertJsonStructure(['csrf_token']);
    }

    public function test_request_with_invalid_csrf_token_returns_403()
    {
        $this->withSession(['_token' => 'valid-token'])
            ->withHeaders([
                'X-CSRF-TOKEN' => 'invalid-token',
                'referer' => 'http://localhost/some-page',
            ])
            ->get('/csrf-token')
            ->assertStatus(403)
            ->assertJson(['message' => 'Invalid CSRF token']);
    }

    public function test_request_without_csrf_token_in_session_returns_403()
    {
        $this->withHeaders([
            'X-CSRF-TOKEN' => 'valid-token',
            'referer' => 'http://localhost/some-page',
        ])
            ->get('/csrf-token')
            ->assertStatus(403)
            ->assertJson(['message' => 'Invalid CSRF token']);
    }

    public function test_request_with_invalid_referer_returns_403()
    {
        $this->withSession(['_token' => 'valid-token'])
            ->withHeaders([
                'X-CSRF-TOKEN' => 'valid-token',
                'referer' => 'http://invalid-url/some-page',
            ])
            ->get('/csrf-token')
            ->assertStatus(403)
            ->assertJson(['message' => 'Invalid request origin']);
    }

    public function test_request_without_referer_returns_403()
    {
        $this->withSession(['_token' => 'valid-token'])
            ->withHeaders([
                'X-CSRF-TOKEN' => 'valid-token',
            ])
            ->get('/csrf-token')
            ->assertStatus(403)
            ->assertJson(['message' => 'Invalid request origin']);
    }
}
