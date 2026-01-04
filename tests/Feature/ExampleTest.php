<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // UBAH DARI 200 MENJADI 302
        // Karena user yang belum login akan di-redirect ke halaman login
        $response->assertStatus(302);
    }
}