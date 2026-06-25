<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogger;
use App\Services\DatabaseBackupService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function __construct(private DatabaseBackupService $backups)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'in:latest,oldest,name_asc,size_desc'],
        ]);

        $search = $filters['search'] ?? '';
        $sort = $filters['sort'] ?? 'latest';
        $backups = collect($this->backups->listBackups())
            ->when($search !== '', function ($backups) use ($search) {
                return $backups->filter(fn (array $backup): bool => str_contains(
                    strtolower($backup['name']),
                    strtolower($search)
                ));
            });

        $backups = match ($sort) {
            'oldest' => $backups->sortBy('modified_at'),
            'name_asc' => $backups->sortBy('name'),
            'size_desc' => $backups->sortByDesc('size'),
            default => $backups->sortByDesc('modified_at'),
        };

        return view('backups.index', [
            'backups' => $backups->values(),
            'filters' => [
                'search' => $search,
                'sort' => $sort,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $filename = $this->backups->create();

        ActivityLogger::log(
            $request,
            'create_backup',
            'backup',
            'Membuat backup database '.$filename.'.',
            null,
            null,
            ['filename' => $filename]
        );

        return redirect()->route('backups.index')->with('success', 'Backup berhasil dibuat.');
    }

    public function download(string $filename): BinaryFileResponse
    {
        $this->abortIfInvalidBackup($filename);

        return response()->download($this->backups->backupPath($filename));
    }

    public function restore(Request $request, string $filename)
    {
        $this->abortIfInvalidBackup($filename);

        $this->backups->restore($filename);

        ActivityLogger::log(
            $request,
            'restore_backup',
            'backup',
            'Restore database dari backup '.$filename.'.',
            null,
            ['filename' => $filename],
            ['restored' => true]
        );

        return redirect()->route('backups.index')->with('success', 'Restore backup berhasil.');
    }

    public function destroy(Request $request, string $filename)
    {
        $this->abortIfInvalidBackup($filename);

        $this->backups->delete($filename);

        ActivityLogger::log(
            $request,
            'delete_backup',
            'backup',
            'Menghapus file backup '.$filename.'.',
            null,
            ['filename' => $filename],
            null
        );

        return redirect()->route('backups.index')->with('success', 'File backup berhasil dihapus.');
    }

    private function abortIfInvalidBackup(string $filename): void
    {
        abort_unless($this->backups->isValidFilename($filename) && $this->backups->exists($filename), 404);
    }
}
