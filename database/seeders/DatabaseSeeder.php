<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@deesa.test'],
            [
                'name' => 'Platform Admin',
                'avatar_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'subscription_status' => 'premium',
                'premium_until' => now()->addMonth(),
                'email_verified_at' => now(),
                'last_seen_at' => now(),
            ]
        );

        $student = User::updateOrCreate(
            ['email' => 'student@deesa.test'],
            [
                'name' => 'Demo Student',
                'avatar_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=300&q=80',
                'password' => Hash::make('password'),
                'role' => UserRole::Student,
                'subscription_status' => 'free',
                'email_verified_at' => now(),
                'last_seen_at' => now(),
            ]
        );

        ActivityLog::updateOrCreate(
            ['user_id' => $student->id, 'event' => 'login', 'session_id' => 'demo-session'],
            [
                'description' => 'Student logged into the platform',
                'started_at' => now()->subDays(2)->subHour(),
                'ended_at' => now()->subDays(2)->subHour()->addMinutes(45),
                'duration_seconds' => 2700,
                'meta' => ['seeded' => true],
            ]
        );

        ActivityLog::updateOrCreate(
            ['user_id' => $student->id, 'event' => 'exam_end', 'session_id' => 'demo-exam-session'],
            [
                'description' => 'Student completed mock exam 1',
                'started_at' => now()->subDays(2),
                'ended_at' => now()->subDays(2)->addHour(),
                'duration_seconds' => 3600,
                'meta' => ['attempt_id' => $attempt->id],
            ]
        );

        Notification::updateOrCreate(
            ['user_id' => $student->id, 'title' => 'Welcome to Deesa UCAT AI'],
            [
                'sent_by' => $admin->id,
                'message' => 'Admin creates and publishes exam mocks from the dashboard.',
                'audience' => 'single',
                'sent_at' => now()->subDay(),
            ]
        );
    }
}
