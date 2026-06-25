<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_portal_pages_require_login(): void
    {
        $desa = $this->makeDesa();

        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('desa.index'))->assertRedirect(route('login'));
        $this->get(route('desa.show', $desa))->assertRedirect(route('login'));
        $this->get(route('perangkat.index'))->assertRedirect(route('login'));
    }

    public function test_viewer_can_view_but_cannot_create_or_edit(): void
    {
        $desa = $this->makeDesa();

        $this->actingAs($this->makeUser('viewer'));

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('desa.index'))->assertOk();
        $this->get(route('desa.show', $desa))->assertOk();
        $this->get(route('desa.create'))->assertForbidden();
        $this->get(route('desa.edit', $desa))->assertForbidden();
    }

    public function test_operator_can_create_and_edit_but_cannot_delete(): void
    {
        $desa = $this->makeDesa();

        $this->actingAs($this->makeUser('operator'));

        $this->get(route('desa.create'))->assertOk();
        $this->get(route('desa.edit', $desa))->assertOk();
        $this->delete(route('desa.destroy', $desa))->assertForbidden();
    }

    public function test_super_admin_can_delete(): void
    {
        $desa = $this->makeDesa();

        $this->actingAs($this->makeUser('super_admin'));

        $this->delete(route('desa.destroy', $desa))->assertRedirect(route('desa.index'));
        $this->assertDatabaseMissing('desas', ['id' => $desa->id]);
    }

    private function makeDesa(): Desa
    {
        $kecamatan = Kecamatan::create([
            'kode_kemendagri' => '35.14.02',
            'nama' => 'Kecamatan Role',
            'kode_pos' => '67101',
        ]);

        $wilayah = Wilayah::create([
            'kecamatan_id' => $kecamatan->id,
            'nama' => 'Desa Role',
            'jenis' => 'desa',
        ]);

        return Desa::create([
            'wilayah_id' => $wilayah->id,
            'kepala_desa' => 'Kepala Role',
        ]);
    }

    private function makeUser(string $role): User
    {
        return User::create([
            'name' => 'User '.$role,
            'email' => $role.'@role.test',
            'password' => 'password',
            'role' => $role,
        ]);
    }
}
