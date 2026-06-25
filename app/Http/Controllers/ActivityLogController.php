<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'module' => ['nullable', 'string', 'max:100'],
            'action' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $search = $filters['search'] ?? null;
        $module = $filters['module'] ?? null;
        $action = $filters['action'] ?? null;
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;
        $searchLike = $search ? '%'.$this->escapeLike($search).'%' : null;

        $logs = ActivityLog::with('user')
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
            ->paginate(15)
            ->withQueryString();  

        return view('activity-logs.index', [
            'logs' => $logs,
            'modules' => collect(['auth', 'desa', 'perangkat', 'users', 'backup', 'settings', 'profile']),
            'actions' => collect(['create', 'update', 'delete', 'login', 'logout', 'upload_foto', 'update_foto', 'register', 'restore']),
            'filters' => [
                'search' => $search ?? '',
                'module' => $module ?? '',
                'action' => $action ?? '',
                'date_from' => $dateFrom ?? '',
                'date_to' => $dateTo ?? '',
            ],
        ]);
    }

    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');

        return view('activity-logs.show', compact('activityLog'));
    }

    private function escapeLike(string $value): string
    {
        return addcslashes($value, '\%_');
    }
}
