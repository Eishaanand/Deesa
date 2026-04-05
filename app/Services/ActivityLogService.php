<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogService
{
    public function log(User $user, array $payload, Request $request): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $user->id,
            'event' => $payload['event'],
            'description' => $payload['description'] ?? null,
            'session_id' => $request->session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'started_at' => $payload['started_at'] ?? now(),
            'ended_at' => $payload['ended_at'] ?? null,
            'duration_seconds' => $payload['duration_seconds'] ?? null,
            'meta' => $payload['meta'] ?? [],
        ]);
    }
}
