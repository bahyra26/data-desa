<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\JabatanPerangkat;
use App\Models\Kecamatan;
use App\Models\PerangkatWilayah;
use App\Models\User;
use App\Models\Wilayah;
use Database\Seeders\JabatanPerangkatSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerangkatWilayahTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_create_second_active_primary_position_in_same_wilayah(): void
    {
        [$kecamatan, $wilayah, $jabatan] = $this->makeWilayahWithPrimaryPosition();
        $this->actingAs($this->makeUser('super_admin'));

        PerangkatWilayah::create([
            'wilayah_id' => $wilayah->id,
            'jabatan_perangkat_id' => $jabatan->id,
            'nama' => 'Perangkat Lama',
            'status' => 'aktif',
        ]);

        $response = $this->post(route('perangkat.store'), [
            'kecamatan_id' => $kecamatan->id,
            'wilayah_id' => $wilayah->id,
            'jabatan_perangkat_id' => $jabatan->id,
            'nama' => 'Perangkat Baru',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('jabatan_perangkat_id');
        $this->assertDatabaseMissing('perangkat_wilayahs', [
            'nama' => 'Perangkat Baru',
            'status' => 'aktif',
        ]);
    }

    public function test_archived_primary_position_does_not_block_new_active_perangkat(): void
    {
        [$kecamatan, $wilayah, $jabatan] = $this->makeWilayahWithPrimaryPosition();
        $this->actingAs($this->makeUser('operator'));

        PerangkatWilayah::create([
            'wilayah_id' => $wilayah->id,
            'jabatan_perangkat_id' => $jabatan->id,
            'nama' => 'Perangkat Lama',
            'status' => 'selesai',
        ]);

        $response = $this->post(route('perangkat.store'), [
            'kecamatan_id' => $kecamatan->id,
            'wilayah_id' => $wilayah->id,
            'jabatan_perangkat_id' => $jabatan->id,
            'nama' => 'Perangkat Baru',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('perangkat.index'));
        $this->assertDatabaseHas('perangkat_wilayahs', [
            'nama' => 'Perangkat Baru',
            'status' => 'aktif',
        ]);
    }

    public function test_removed_position_names_are_not_seeded(): void
    {
        $this->seed(JabatanPerangkatSeeder::class);

        $this->assertDatabaseHas('jabatan_perangkats', ['nama' => 'Kepala Desa']);
        $this->assertSame(9, JabatanPerangkat::count());
    }

    private function makeWilayahWithPrimaryPosition(): array
    {
        $this->seed(JabatanPerangkatSeeder::class);

        $kecamatan = Kecamatan::create([
            'kode_kemendagri' => '35.14.01',
            'nama' => 'Kecamatan Uji',
            'kode_pos' => '67100',
        ]);

        $wilayah = Wilayah::create([
            'kecamatan_id' => $kecamatan->id,
            'nama' => 'Desa Uji',
            'jenis' => 'desa',
        ]);

        Desa::create([
            'wilayah_id' => $wilayah->id,
            'kepala_desa' => 'Perangkat Lama',
        ]);

        return [
            $kecamatan,
            $wilayah,
            JabatanPerangkat::where('nama', 'Kepala Desa')->firstOrFail(),
        ];
    }

    private function makeUser(string $role): User
    {
        return User::create([
            'name' => 'User '.$role,
            'email' => $role.'@example.test',
            'password' => 'password',
            'role' => $role,
        ]);
    }
}
