<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin login page is accessible.
     */
    public function test_admin_login_page_is_accessible(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    /**
     * Test unauthenticated access to admin dashboard redirects to login.
     */
    public function test_admin_dashboard_requires_authentication(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test authenticated admin can access dashboard.
     */
    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $admin = User::create([
            'nama'        => 'Admin Setor.in',
            'email'       => 'admin@setorin.com',
            'no_telepon'  => '081234567890',
            'password'    => bcrypt('admin123'),
            'role'        => 'admin',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($admin, 'web')->get('/admin');
        $response->assertStatus(200);
    }

    /**
     * Test authenticated petugas can access dashboard.
     */
    public function test_authenticated_petugas_can_access_dashboard(): void
    {
        $petugas = User::create([
            'nama'        => 'Petugas Setor.in',
            'email'      => 'petugas@setorin.com',
            'no_telepon' => '081234567891',
            'password'   => bcrypt('petugas123'),
            'role'       => 'petugas',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($petugas, 'web')->get('/petugas');
        $response->assertStatus(200);
    }

    /**
     * Test unauthenticated petugas access redirects to login.
     */
    public function test_petugas_dashboard_requires_authentication(): void
    {
        $response = $this->get('/petugas');
        $response->assertRedirect('/petugas/login');
    }
}
