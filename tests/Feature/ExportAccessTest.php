<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_export(): void
    {
        $desa = $this->makeDesa();

        $this->get(route('desa.export.excel'))->assertRedirect(route('login'));
        $this->get(route('desa.export.list-pdf'))->assertRedirect(route('login'));
        $this->get(route('perangkat.export.excel'))->assertRedirect(route('login'));
        $this->get(route('perangkat.export.pdf'))->assertRedirect(route('login'));
        $this->get(route('activity-logs.export.excel'))->assertRedirect(route('login'));
        $this->get(route('activity-logs.export.pdf'))->assertRedirect(route('login'));
        $this->get(route('desa.export.pdf', $desa))->assertRedirect(route('login'));
    }

    public function test_viewer_can_export_desa_and_perangkat(): void
    {
        $this->makeDesa();
        $this->actingAs($this->makeUser('viewer', 'viewer@example.test'));

        $this->get(route('desa.export.excel'))->assertOk();
        $this->get(route('desa.export.list-pdf'))->assertOk();
        $this->get(route('perangkat.export.excel'))->assertOk();
        $this->get(route('perangkat.export.pdf'))->assertOk();
    }

    public function test_operator_can_export_desa_and_perangkat(): void
    {
        $this->makeDesa();
        $this->actingAs($this->makeUser('operator', 'operator@example.test'));

        $this->get(route('desa.export.excel'))->assertOk();
        $this->get(route('desa.export.list-pdf'))->assertOk();
        $this->get(route('perangkat.export.excel'))->assertOk();
        $this->get(route('perangkat.export.pdf'))->assertOk();
    }

    public function test_operator_and_viewer_cannot_export_activity_logs(): void
    {
        ActivityLog::create([
            'action' => 'login',
            'module' => 'auth',
            'description' => 'User login.',
        ]);

        $this->actingAs($this->makeUser('operator', 'operator@example.test'));
        $this->get(route('activity-logs.export.excel'))->assertForbidden();
        $this->get(route('activity-logs.export.pdf'))->assertForbidden();

        $this->actingAs($this->makeUser('viewer', 'viewer@example.test'));
        $this->get(route('activity-logs.export.excel'))->assertForbidden();
        $this->get(route('activity-logs.export.pdf'))->assertForbidden();
    }

    public function test_super_admin_can_export_activity_logs(): void
    {
        ActivityLog::create([
            'action' => 'login',
            'module' => 'auth',
            'description' => 'User login.',
        ]);

        $this->actingAs($this->makeUser('super_admin', 'admin@example.test'));

        $this->get(route('activity-logs.export.excel'))->assertOk();
        $this->get(route('activity-logs.export.pdf'))->assertOk();
    }

    public function test_logged_in_user_can_export_desa_detail_pdf(): void
    {
        $desa = $this->makeDesa();

        $this->actingAs($this->makeUser('viewer', 'viewer@example.test'));

        $this->get(route('desa.export.pdf', $desa))->assertOk();
    }

    private function makeDesa(): Desa
    {
        $kecamatan = Kecamatan::create([
            'kode_kemendagri' => '35.14.99',
            'nama' => 'Kecamatan Export',
            'kode_pos' => '67199',
        ]);

        $wilayah = Wilayah::create([
            'kecamatan_id' => $kecamatan->id,
            'nama' => 'Desa Export',
            'jenis' => 'desa',
        ]);

        return Desa::create([
            'wilayah_id' => $wilayah->id,
            'alamat_kantor' => 'Kantor Desa Export',
            'kepala_desa' => 'Kepala Export',
            'jumlah_penduduk' => 100,
            'luas_wilayah' => 1.25,
        ]);
    }

    private function makeUser(string $role, string $email): User
    {
        return User::create([
            'name' => 'User '.$role,
            'email' => $email,
            'password' => 'password',
            'role' => $role,
        ]);
    }
}
