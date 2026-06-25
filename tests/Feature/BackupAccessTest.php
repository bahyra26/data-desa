<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\DatabaseBackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BackupAccessTest extends TestCase
{
    use RefreshDatabase;

    private string $backupPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->backupPath = storage_path('framework/testing/backups');

        $backupPath = $this->backupPath;
        $this->app->instance(DatabaseBackupService::class, new class($backupPath) extends DatabaseBackupService {
            public function __construct(private string $testBackupPath)
            {
            }

            public function backupPath(?string $filename = null): string
            {
                if (! File::exists($this->testBackupPath)) {
                    File::makeDirectory($this->testBackupPath, 0755, true);
                }

                return $filename ? $this->testBackupPath.DIRECTORY_SEPARATOR.$filename : $this->testBackupPath;
            }
        });
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->backupPath);

        parent::tearDown();
    }

    public function test_backups_require_super_admin(): void
    {
        $this->get(route('backups.index'))->assertRedirect(route('login'));

        $this->actingAs($this->makeUser('operator', 'operator@example.test'));
        $this->get(route('backups.index'))->assertForbidden();
        $this->post(route('backups.store'))->assertForbidden();

        $this->actingAs($this->makeUser('viewer', 'viewer@example.test'));
        $this->get(route('backups.index'))->assertForbidden();
    }

    public function test_super_admin_can_view_and_create_backup(): void
    {
        $this->actingAs($this->makeUser('super_admin', 'admin@example.test'));

        $this->get(route('backups.index'))
            ->assertOk()
            ->assertSee('Backup Database');

        $this->post(route('backups.store'))
            ->assertRedirect(route('backups.index'))
            ->assertSessionHas('success');

        $this->assertNotEmpty(File::glob($this->backupPath.'/backup-*.json'));
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
