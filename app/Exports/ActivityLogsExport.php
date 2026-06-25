<?php

namespace App\Exports;

use App\Models\ActivityLog;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ActivityLogsExport implements FromCollection, WithHeadings
{
    public function __construct(private array $filters = [])
    {
    }

    public function collection(): Collection
    {
        $search = $this->filters['search'] ?? null;
        $module = $this->filters['module'] ?? null;
        $action = $this->filters['action'] ?? null;
        $dateFrom = $this->filters['date_from'] ?? null;
        $dateTo = $this->filters['date_to'] ?? null;
        $searchLike = $search ? '%'.$this->escapeLike($search).'%' : null;

        return ActivityLog::with('user')
            ->when($searchLike, function ($query, string $searchLike) {
                $query->where(function ($query) use ($searchLike) {
                    $query->where('action', 'like', $searchLike)
                        ->orWhere('module', 'like', $searchLike)
                        ->orWhere('description', 'like', $searchLike)
                        ->orWhereHas('user', function ($query) use ($searchLike) {
                            $query->where('name', 'like', $searchLike)
                                ->orWhere('email', 'like', $searchLike)
                                ->orWhere('role', 'like', $searchLike);
                        });
                });
            })
            ->when($module, fn ($query, string $module) => $query->where('module', $module))
            ->when($action, fn ($query, string $action) => $query->where('action', $action))
            ->when($dateFrom, fn ($query, string $dateFrom) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query, string $dateTo) => $query->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->get()
            ->map(fn (ActivityLog $log): array => [
                'waktu' => optional($log->created_at)->format('d/m/Y H:i:s'),
                'user' => $log->user?->name ?? 'System',
                'role' => $log->user ? str_replace('_', ' ', $log->user->role) : '-',
                'action' => $log->action,
                'module' => $log->module,
                'description' => $log->description,
                'ip_address' => $log->ip_address,
            ]);
    }

    public function headings(): array
    {
        return [
            'Waktu',
            'User',
            'Role',
            'Action',
            'Module',
            'Description',
            'IP Address',
        ];
    }

    private function escapeLike(string $value): string
    {
        return addcslashes($value, '\%_');
    }
}
