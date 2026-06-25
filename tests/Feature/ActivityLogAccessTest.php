<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_logs_require_super_admin(): void
    {
        $log = ActivityLog::create([
            'action' => 'login',
            'module' => 'auth',
            'description' => 'User login ke portal.',
        ]);

        $this->get(route('activity-logs.index'))->assertRedirect(route('login'));

        $this->actingAs($this->makeUser('operator', 'operator@example.test'));
        $this->get(route('activity-logs.index'))->assertForbidden();
        $this->get(route('activity-logs.show', $log))->assertForbidden();

        $this->actingAs($this->makeUser('viewer', 'viewer@example.test'));
        $this->get(route('activity-logs.index'))->assertForbidden();
    }

    public function test_super_admin_can_view_activity_logs(): void
    {
        ActivityLog::create([
            'action' => 'login',
            'module' => 'auth',
            'description' => 'User login ke portal.',
        ]);

        $this->actingAs($this->makeUser('super_admin', 'admin@example.test'));

        $this->get(route('activity-logs.index'))
            ->assertOk()
            ->assertSee('Audit Log')
            ->assertSee('User login ke portal.');
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
