<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Halaman awal publik dapat diakses dan halaman terproteksi redirect ke login.
     */
    public function test_public_homepage_is_accessible_and_protected_pages_redirect_to_login(): void
    {
        // Halaman awal publik (Portal Pengaduan & Layanan Sarpras)
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);

        // Halaman login memiliki tombol panah kembali ke halaman publik
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('Kembali ke Halaman Publik');
        $loginResponse->assertSee(route('welcome'));

        // Halaman dashboard yang butuh login akan me-redirect guest ke /login
        $dashboardResponse = $this->get('/dashboard');
        $dashboardResponse->assertRedirect('/login');
    }
}
