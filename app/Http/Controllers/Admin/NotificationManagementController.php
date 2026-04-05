<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.notifications.index', [
            'notifications' => Notification::with(['user', 'sender'])->latest()->paginate(20),
            'users' => User::orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'audience' => ['required', 'in:all,premium,free,single'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $recipients = match ($validated['audience']) {
            'single' => User::whereKey($validated['user_id'])->get(),
            'premium' => User::where('subscription_status', 'premium')->get(),
            'free' => User::where('subscription_status', 'free')->get(),
            default => User::all(),
        };

        foreach ($recipients as $recipient) {
            Notification::create([
                'user_id' => $recipient->id,
                'sent_by' => $request->user()->id,
                'title' => $validated['title'],
                'message' => $validated['message'],
                'audience' => $validated['audience'],
                'sent_at' => now(),
            ]);
        }

        return redirect()->route('admin.notifications.index')->with('status', 'Notification sent.');
    }
}
