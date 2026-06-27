<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLogger
{
    public static function log(
        Request $request,
        string $action,
        string $module,
        string $description,
        ?Model $subject = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): ActivityLog {
        $userId = optional($request->user())->id;
        $userId = $userId && User::whereKey($userId)->exists() ? $userId : null;

        return ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'old_values' => self::cleanValues($oldValues),
            'new_values' => self::cleanValues($newValues),
            'ip_address' => $request->ip(),
            'user_agent' => substr(preg_replace('/[[:cntrl:]]/', '', $request->userAgent() ?? ''), 0, 500),
        ]);
    }

    private static function cleanValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        foreach (['password', 'remember_token'] as $hiddenField) {
            unset($values[$hiddenField]);
        }

        return $values;
    }
}
