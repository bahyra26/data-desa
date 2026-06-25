<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_management_requires_super_admin(): void
    {
        $targetUser = $this->makeUser('viewer', 'target@example.test');

        $this->get(route('users.index'))->assertRedirect(route('login'));

        $this->actingAs($this->makeUser('operator', 'operator@example.test'));
        $this->get(route('users.index'))->assertForbidden();
        $this->get(route('users.create'))->assertForbidden();
        $this->get(route('users.edit', $targetUser))->assertForbidden();
        $this->post(route('users.store'), [])->assertForbidden();
        $this->put(route('users.update', $targetUser), [])->assertForbidden();
        $this->delete(route('users.destroy', $targetUser))->assertForbidden();

        $this->actingAs($this->makeUser('viewer', 'viewer@example.test'));
        $this->get(route('users.index'))->assertForbidden();
    }

    public function test_super_admin_can_create_update_and_reset_user_password(): void
    {
        $this->actingAs($this->makeUser('super_admin', 'admin@example.test'));

        $this->post(route('users.store'), [
            'name' => 'Operator Baru',
            'email' => 'operatorbaru@example.test',
            'password' => 'OperatorBaru!2026',
            'role' => 'operator',
        ])->assertRedirect(route('users.index'));

        $user = User::where('email', 'operatorbaru@example.test')->firstOrFail();
        $this->assertTrue(Hash::check('OperatorBaru!2026', $user->password));

        $this->put(route('users.update', $user), [
            'name' => 'Viewer Baru',
            'email' => 'viewerbaru@example.test',
            'password' => 'ViewerReset!2026',
            'role' => 'viewer',
        ])->assertRedirect(route('users.index'));

        $user->refresh();
        $this->assertSame('Viewer Baru', $user->name);
        $this->assertSame('viewerbaru@example.test', $user->email);
        $this->assertSame('viewer', $user->role);
        $this->assertTrue(Hash::check('ViewerReset!2026', $user->password));
    }

    public function test_super_admin_cannot_create_user_with_weak_password(): void
    {
        $this->actingAs($this->makeUser('super_admin', 'admin@example.test'));

        $this->post(route('users.store'), [
            'name' => 'Operator Lemah',
            'email' => 'operatorlemah@example.test',
            'password' => 'password',
            'role' => 'operator',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'operatorlemah@example.test']);
    }

    public function test_super_admin_cannot_delete_self(): void
    {
        $admin = $this->makeUser('super_admin', 'admin@example.test');

        $this->actingAs($admin);

        $this->delete(route('users.destroy', $admin))
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_super_admin_can_delete_other_user(): void
    {
        $admin = $this->makeUser('super_admin', 'admin@example.test');
        $operator = $this->makeUser('operator', 'operator@example.test');

        $this->actingAs($admin);

        $this->delete(route('users.destroy', $operator))->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $operator->id]);
    }

    public function test_super_admin_can_search_filter_sort_and_paginate_users(): void
    {
        $admin = $this->makeUser('super_admin', 'admin@example.test');
        $this->makeUser('operator', 'zeta@example.test', 'Zeta Operator');
        $this->makeUser('viewer', 'alpha@example.test', 'Alpha Viewer');

        for ($index = 1; $index <= 12; $index++) {
            $this->makeUser('viewer', "viewer{$index}@example.test", "Viewer {$index}");
        }

        $this->actingAs($admin);

        $this->get(route('users.index', [
            'search' => 'alpha',
            'role' => 'viewer',
            'sort' => 'email_asc',
        ]))
            ->assertOk()
            ->assertSee('Alpha Viewer')
            ->assertDontSee('Zeta Operator')
            ->assertSee('value="alpha"', false)
            ->assertSee('value="viewer" selected', false)
            ->assertSee('value="email_asc" selected', false);

        $this->get(route('users.index', [
            'role' => 'viewer',
            'sort' => 'latest',
        ]))
            ->assertOk()
            ->assertSee('Halaman 1')
            ->assertSee('Berikutnya')
            ->assertSee('role=viewer')
            ->assertSee('sort=latest');
    }

    public function test_user_menu_links_are_visible_by_role(): void
    {
        $admin = $this->makeUser('super_admin', 'admin@example.test');
        $operator = $this->makeUser('operator', 'operator@example.test');

        $this->actingAs($admin);
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Settings / Profil Saya')
            ->assertSee('Manajemen User')
            ->assertSee('data-user-menu-toggle');

        $this->actingAs($operator);
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Settings / Profil Saya')
            ->assertDontSee('Manajemen User');
    }

    private function makeUser(string $role, string $email, ?string $name = null): User
    {
        return User::create([
            'name' => $name ?? 'User '.$role,
            'email' => $email,
            'password' => 'password',
            'role' => $role,
        ]);
    }
}
