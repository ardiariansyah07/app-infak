<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk();
    }

    public function test_petugas_cannot_access_admin_master_data(): void
    {
        $petugas = User::factory()->create(['role' => User::ROLE_PETUGAS]);

        $this->actingAs($petugas)
            ->get('/admin/siswa')
            ->assertForbidden();
    }

    public function test_admin_can_access_report_menu(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get('/admin/laporan')
            ->assertOk();
    }

    public function test_parent_cannot_access_petugas_payment_area(): void
    {
        $parent = User::factory()->create(['role' => User::ROLE_ORANG_TUA]);

        $this->actingAs($parent)
            ->get('/petugas/pembayaran')
            ->assertForbidden();
    }
}
