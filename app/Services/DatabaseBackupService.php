<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class DatabaseBackupService
{
    private const TABLES = [
        'kecamatans',
        'wilayahs',
        'desas',
        'jabatan_perangkats',
        'perangkat_wilayahs',
        'users',
        'activity_logs',
    ];

    private const RESTORE_ORDER = [
        'activity_logs',
        'perangkat_wilayahs',
        'desas',
        'jabatan_perangkats',
        'wilayahs',
        'kecamatans',
        'users',
    ];

    public function backupPath(?string $filename = null): string
    {
        $path = storage_path('app/backups');

        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        return $filename ? $path.DIRECTORY_SEPARATOR.$filename : $path;
    }

    public function listBackups(): array
    {
        return collect(File::files($this->backupPath()))
            ->filter(fn ($file) => $file->getExtension() === 'json')
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(fn ($file) => [
                'name' => $file->getFilename(),
                'size' => $file->getSize(),
                'modified_at' => date('Y-m-d H:i:s', $file->getMTime()),
            ])
            ->values()
            ->all();
    }

    public function create(): string
    {
        $filename = 'backup-'.now()->format('Ymd-His').'.json';
        $payload = [
            'generated_at' => now()->toDateTimeString(),
            'tables' => [],
        ];

        foreach (self::TABLES as $table) {
            $payload['tables'][$table] = DB::table($table)->orderBy('id')->get()
                ->map(function ($row) use ($table) {
                    $row = (array) $row;

                    if ($table === 'users') {
                        $row['remember_token'] = null;
                    }

                    return $row;
                })
                ->all();
        }

        File::put(
            $this->backupPath($filename),
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        return $filename;
    }

    private const REQUIRED_COLUMNS = [
        'kecamatans' => ['id', 'kode_kemendagri', 'nama', 'kode_pos', 'created_at', 'updated_at'],
        'wilayahs' => ['id', 'kecamatan_id', 'nama', 'jenis', 'created_at', 'updated_at'],
        'desas' => ['id', 'wilayah_id', 'alamat_kantor', 'kepala_desa', 'jumlah_penduduk', 'luas_wilayah', 'created_at', 'updated_at'],
        'jabatan_perangkats' => ['id', 'nama', 'level_urutan', 'created_at', 'updated_at'],
        'perangkat_wilayahs' => ['id', 'wilayah_id', 'jabatan_perangkat_id', 'nama', 'foto', 'jenis_kelamin', 'nomor_hp', 'email', 'mulai_menjabat', 'akhir_menjabat', 'status', 'created_at', 'updated_at'],
        'users' => ['id', 'name', 'email', 'email_verified_at', 'password', 'remember_token', 'role', 'foto_profil', 'created_at', 'updated_at'],
        'activity_logs' => ['id', 'user_id', 'action', 'module', 'description', 'subject_type', 'subject_id', 'old_values', 'new_values', 'ip_address', 'user_agent', 'created_at', 'updated_at'],
    ];

    public function restore(string $filename): void
    {
        $path = $this->backupPath($filename);

        if (! File::exists($path)) {
            throw new RuntimeException('File backup tidak ditemukan.');
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload) || ! isset($payload['tables']) || ! is_array($payload['tables'])) {
            throw new RuntimeException('Format file backup tidak valid.');
        }

        $this->validateBackupPayload($payload);

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        }

        DB::transaction(function () use ($payload) {
            foreach (self::RESTORE_ORDER as $table) {
                DB::table($table)->delete();
            }

            foreach (self::TABLES as $table) {
                foreach ($payload['tables'][$table] ?? [] as $row) {
                    DB::table($table)->insert($row);
                }
            }

            $this->resetSqliteSequences();
        });

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    /**
     * Validate backup payload structure before restoring.
     */
    private function validateBackupPayload(array $payload): void
    {
        if (! isset($payload['generated_at']) || ! is_string($payload['generated_at'])) {
            throw new RuntimeException('Format file backup tidak valid: field generated_at tidak ditemukan.');
        }

        $missingTables = array_diff(self::TABLES, array_keys($payload['tables']));
        if ($missingTables) {
            throw new RuntimeException(
                'Format file backup tidak valid: tabel berikut tidak ditemukan: '.implode(', ', $missingTables).'.'
            );
        }

        foreach (self::TABLES as $table) {
            $rows = $payload['tables'][$table] ?? [];

            if (! is_array($rows)) {
                throw new RuntimeException(
                    "Format file backup tidak valid: data tabel {$table} bukan array."
                );
            }

            foreach ($rows as $index => $row) {
                if (! is_array($row)) {
                    throw new RuntimeException(
                        "Format file backup tidak valid: baris ke-".($index + 1)." pada tabel {$table} bukan array."
                    );
                }

                $required = self::REQUIRED_COLUMNS[$table];
                $missing = array_diff($required, array_keys($row));
                $missingFirst = array_slice($missing, 0, 5);
                if ($missingFirst) {
                    throw new RuntimeException(
                        "Format file backup tidak valid: baris ke-".($index + 1)." pada tabel {$table} ".
                        'tidak memiliki kolom: '.implode(', ', $missingFirst).'.'
                    );
                }

                // Strip unknown columns that may exist in older backups (e.g. latitude/longitude)
                // to avoid SQL errors on insert.
                $allowed = array_merge($required, ['email_verified_at', 'remember_token', 'foto_profil', 'foto', 'alamat_kantor', 'latitude', 'longitude', 'created_at', 'updated_at']);
                $extra = array_diff(array_keys($row), $allowed);
                if ($extra) {
                    foreach ($extra as $col) {
                        unset($payload['tables'][$table][$index][$col]);
                    }
                }
            }
        }
    }

    public function delete(string $filename): void
    {
        $path = $this->backupPath($filename);

        if (! File::exists($path)) {
            throw new RuntimeException('File backup tidak ditemukan.');
        }

        File::delete($path);
    }

    public function exists(string $filename): bool
    {
        return File::exists($this->backupPath($filename));
    }

    public function isValidFilename(string $filename): bool
    {
        return preg_match('/^backup-\d{8}-\d{6}\.json$/', $filename) === 1;
    }

    private function resetSqliteSequences(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        foreach (self::TABLES as $table) {
            $maxId = DB::table($table)->max('id') ?? 0;
            DB::table('sqlite_sequence')->updateOrInsert(
                ['name' => $table],
                ['seq' => $maxId]
            );
        }
    }
}
