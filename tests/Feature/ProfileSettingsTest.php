<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_profile_requires_login(): void
    {
        $this->get(route('settings.profile'))->assertRedirect(route('login'));
    }

    public function test_user_can_update_own_profile_without_changing_role(): void
    {
        Storage::fake('public');

        $user = $this->makeUser('operator', 'operator@example.test');

        $this->actingAs($user);

        $this->put(route('settings.profile.update'), [
            'name' => 'Operator Update',
            'email' => 'operator.update@example.test',
            'role' => 'super_admin',
            'foto_profil' => $this->fakePngUpload('profile.png'),
        ])->assertRedirect(route('settings.profile'))
            ->assertSessionHas('success');

        $user->refresh();

        $this->assertSame('Operator Update', $user->name);
        $this->assertSame('operator.update@example.test', $user->email);
        $this->assertSame('operator', $user->role);
        $this->assertNotNull($user->foto_profil);
        Storage::disk('public')->assertExists($user->foto_profil);
    }

    public function test_uploading_new_profile_photo_deletes_old_photo(): void
    {
        Storage::fake('public');

        $oldPhoto = 'user-profiles/old.png';
        Storage::disk('public')->put($oldPhoto, base64_decode($this->tinyPngBase64()));
        $user = $this->makeUser('viewer', 'viewer@example.test', ['foto_profil' => $oldPhoto]);

        $this->actingAs($user);

        $this->put(route('settings.profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'foto_profil' => $this->fakePngUpload('new.png'),
        ])->assertRedirect(route('settings.profile'));

        $user->refresh();

        Storage::disk('public')->assertMissing($oldPhoto);
        Storage::disk('public')->assertExists($user->foto_profil);
    }

    public function test_user_can_update_password_with_current_password(): void
    {
        $user = $this->makeUser('viewer', 'viewer@example.test');

        $this->actingAs($user);

        $this->put(route('settings.password.update'), [
            'current_password' => 'password',
            'password' => 'PasswordBaru!2026',
            'password_confirmation' => 'PasswordBaru!2026',
        ])->assertRedirect(route('settings.profile'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('PasswordBaru!2026', $user->refresh()->password));
    }

    public function test_password_update_rejects_weak_password(): void
    {
        $user = $this->makeUser('viewer', 'viewer@example.test');

        $this->actingAs($user);

        $this->put(route('settings.password.update'), [
            'current_password' => 'password',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('password', $user->refresh()->password));
    }

    public function test_password_update_rejects_wrong_current_password(): void
    {
        $user = $this->makeUser('viewer', 'viewer@example.test');

        $this->actingAs($user);

        $this->put(route('settings.password.update'), [
            'current_password' => 'salah',
            'password' => 'PasswordBaru!2026',
            'password_confirmation' => 'PasswordBaru!2026',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password', $user->refresh()->password));
    }

    private function makeUser(string $role, string $email, array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'User '.$role,
            'email' => $email,
            'password' => 'password',
            'role' => $role,
        ], $attributes));
    }

    private function fakePngUpload(string $name): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'profile-upload-');
        file_put_contents($path, base64_decode($this->tinyPngBase64()));

        return new UploadedFile($path, $name, 'image/png', null, true);
    }

    private function tinyPngBase64(): string
    {
        return 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=';
    }
}
